<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = 10; // DEFAULT 10 PER HALAMAN

        $reportData = [];
        $reportTitle = '';

        if ($reportType) {
            switch ($reportType) {
                case 'transaction':
                    $reportTitle = 'Laporan Transaksi Stok';
                    $reportData = $this->getTransactionReport($startDate, $endDate, $perPage);
                    break;
                case 'stock':
                    $reportTitle = 'Laporan Stok Produk';
                    $reportData = $this->getStockReport($perPage);
                    break;
                case 'category':
                    $reportTitle = 'Laporan Kategori';
                    $reportData = $this->getCategoryReport($perPage);
                    break;
                case 'damaged':
                    $reportTitle = 'Laporan Barang Rusak';
                    $reportData = $this->getDamagedReport($startDate, $endDate, $perPage);
                    break;
            }
        }

        return view('contents.reports', compact('reportData', 'reportType', 'startDate', 'endDate', 'reportTitle', 'perPage'));
    }

    public function downloadPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'stock');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $reportData = [];
        $reportTitle = '';

        switch ($reportType) {
            case 'transaction':
                $reportTitle = 'Laporan Transaksi Stok';
                $reportData = $this->getTransactionReportAll($startDate, $endDate);
                break;
            case 'stock':
                $reportTitle = 'Laporan Stok Produk';
                $reportData = $this->getStockReportAll();
                break;
            case 'category':
                $reportTitle = 'Laporan Kategori';
                $reportData = $this->getCategoryReportAll();
                break;
            case 'damaged':
                $reportTitle = 'Laporan Barang Rusak';
                $reportData = $this->getDamagedReportAll($startDate, $endDate);
                break;
            default:
                $reportTitle = 'Laporan Stok Produk';
                $reportData = $this->getStockReportAll();
                break;
        }

        $pdf = Pdf::loadView('pdf.report-pdf', [
            'reportData' => $reportData,
            'reportType' => $reportType,
            'reportTitle' => $reportTitle,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_' . $reportType . '_' . date('Ymd_His') . '.pdf');
    }

    private function getTransactionReport($startDate, $endDate, $perPage)
    {
        $query = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'stock_transactions.transaction_date',
                'products.product_code',
                'products.name as product_name',
                'categories.name as category_name',
                'stock_transactions.type',
                'stock_transactions.quantity',
                'stock_transactions.condition',
                'stock_transactions.created_by',
                'stock_transactions.stock'
            )
            ->orderBy('stock_transactions.transaction_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereDate('stock_transactions.transaction_date', '>=', $startDate)
                ->whereDate('stock_transactions.transaction_date', '<=', $endDate);
        }

        $results = $query->paginate($perPage);
        
        $results->getCollection()->transform(function ($item) {
            return [
                'date' => \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d/m/Y H:i'),
                'product_code' => $item->product_code,
                'product' => $item->product_name,
                'category' => $item->category_name,
                'type' => $item->type == 'in' ? 'Masuk' : 'Keluar',
                'quantity' => $item->quantity,
                'condition' => $item->condition == 'good' ? 'Aman' : 'Rusak',
                'user' => $this->getUserName($item->created_by),
                'stock' => $item->stock ?? 0
            ];
        });
        
        return $results;
    }

    private function getStockReport($perPage)
    {
        $results = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_deleted', false)
            ->select(
                'products.product_code',
                'products.name as product_name',
                'categories.name as category_name',
                'products.stock'
            )
            ->orderBy('products.name', 'asc')
            ->paginate($perPage);

        $results->getCollection()->transform(function ($item) {
            return [
                'code' => $item->product_code ?? '-',
                'product' => $item->product_name,
                'category' => $item->category_name,
                'stock' => $item->stock ?? 0,
                'status' => $item->stock <= 5 ? '⚠️ Stok Menipis' : ($item->stock == 0 ? '❌ Habis' : '✅ Aman')
            ];
        });

        return $results;
    }

    private function getCategoryReport($perPage)
    {
        $results = DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->where('products.is_deleted', false)
            ->select(
                'categories.name as category_name',
                DB::raw('COUNT(products.id) as total_products'),
                DB::raw('SUM(products.stock) as total_stock')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->paginate($perPage);

        $results->getCollection()->transform(function ($item) {
            return [
                'category' => $item->category_name,
                'total_products' => $item->total_products,
                'total_stock' => $item->total_stock ?? 0
            ];
        });

        return $results;
    }

    private function getDamagedReport($startDate, $endDate, $perPage)
    {
        $query = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select(
                'stock_transactions.transaction_date',
                'products.product_code',
                'products.name as product_name',
                'stock_transactions.quantity',
                'stock_transactions.created_by'
            )
            ->where('stock_transactions.condition', 'damaged')
            ->orderBy('stock_transactions.transaction_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereDate('stock_transactions.transaction_date', '>=', $startDate)
                ->whereDate('stock_transactions.transaction_date', '<=', $endDate);
        }

        $results = $query->paginate($perPage);

        $results->getCollection()->transform(function ($item) {
            return [
                'date' => \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d/m/Y H:i'),
                'product_code' => $item->product_code,
                'product' => $item->product_name,
                'quantity' => $item->quantity,
                'user' => $this->getUserName($item->created_by)
            ];
        });

        return $results;
    }

    // ========== UNTUK PDF (AMBIL SEMUA DATA) ==========

    private function getTransactionReportAll($startDate, $endDate)
    {
        $query = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'stock_transactions.transaction_date',
                'products.product_code',
                'products.name as product_name',
                'categories.name as category_name',
                'stock_transactions.type',
                'stock_transactions.quantity',
                'stock_transactions.condition',
                'stock_transactions.created_by',
                'stock_transactions.stock'
            )
            ->orderBy('stock_transactions.transaction_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereDate('stock_transactions.transaction_date', '>=', $startDate)
                ->whereDate('stock_transactions.transaction_date', '<=', $endDate);
        }

        return $query->get()->map(fn($item) => [
            'date' => \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d/m/Y H:i'),
            'product_code' => $item->product_code,
            'product' => $item->product_name,
            'category' => $item->category_name,
            'type' => $item->type == 'in' ? 'Masuk' : 'Keluar',
            'quantity' => $item->quantity,
            'condition' => $item->condition == 'good' ? 'Aman' : 'Rusak',
            'user' => $this->getUserName($item->created_by),
            'stock' => $item->stock ?? 0
        ])->toArray();
    }

    private function getStockReportAll()
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_deleted', false)
            ->select(
                'products.product_code',
                'products.name as product_name',
                'categories.name as category_name',
                'products.stock'
            )
            ->orderBy('products.name', 'asc')
            ->get()
            ->map(fn($item) => [
                'code' => $item->product_code ?? '-',
                'product' => $item->product_name,                'category' => $item->category_name,
                'stock' => $item->stock ?? 0,
                'status' => $item->stock <= 5 ? '⚠️ Stok Menipis' : ($item->stock == 0 ? '❌ Habis' : '✅ Aman')
            ])->toArray();
    }

    private function getCategoryReportAll()
    {
        return DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->where('products.is_deleted', false)
            ->select(
                'categories.name as category_name',
                DB::raw('COUNT(products.id) as total_products'),
                DB::raw('SUM(products.stock) as total_stock')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category_name,
                'total_products' => $item->total_products,
                'total_stock' => $item->total_stock ?? 0
            ])->toArray();
    }

    private function getDamagedReportAll($startDate, $endDate)
    {
        $query = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select(
                'stock_transactions.transaction_date',
                'products.product_code',
                'products.name as product_name',
                'stock_transactions.quantity',
                'stock_transactions.created_by'
            )
            ->where('stock_transactions.condition', 'damaged')
            ->orderBy('stock_transactions.transaction_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereDate('stock_transactions.transaction_date', '>=', $startDate)
                ->whereDate('stock_transactions.transaction_date', '<=', $endDate);
        }

        return $query->get()->map(fn($item) => [
            'date' => \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d/m/Y H:i'),
            'product_code' => $item->product_code,
            'product' => $item->product_name,
            'quantity' => $item->quantity,
            'user' => $this->getUserName($item->created_by)
        ])->toArray();
    }

    private function getUserName($userId)
    {
        if (!$userId) return 'System';
        $user = DB::table('users')->where('id', $userId)->first();
        return $user ? ($user->username ?? 'Unknown') : 'Unknown';
    }
}