<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->role_name ?? 'operator';

        // ========== DATA UMUM ==========
        $totalProducts = DB::table('products')->where('is_deleted', 0)->count();
        $totalCategories = DB::table('categories')->count();
        $totalStock = DB::table('stock_transactions')
            ->selectRaw("SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) - SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) AS total_stock")
            ->value('total_stock') ?? 0;

        // ========== DATA KHUSUS ADMIN & SUPER ADMIN ==========
        $totalUsers = 0;
        $userStats = collect([]);
        if (in_array($role, ['admin', 'super_admin'])) {
            $totalUsers = DB::table('users')->count();
            $userStats = DB::table('users')
                ->selectRaw("DATE_FORMAT(created_at, '%m') as month, COUNT(*) as total")
                ->whereNotNull('created_at')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // ========== CHART PRODUK (12 BULAN) ==========
        $months = [];
        $inData = [];
        $outData = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthDate = date('Y-m-01', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($monthDate));
            $monthStart = date('Y-m-01', strtotime($monthDate));
            $monthEnd = date('Y-m-t', strtotime($monthDate));
            
            $inData[] = DB::table('stock_transactions')->where('type', 'in')->whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('quantity') ?? 0;
            $outData[] = DB::table('stock_transactions')->where('type', 'out')->whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('quantity') ?? 0;
        }

        // ========== RANKING BARANG ==========
        $rankingProducts = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select('products.id', 'products.product_code', 'products.name', DB::raw('COUNT(*) as transaction_count'))
            ->where('products.is_deleted', 0)
            ->groupBy('products.id', 'products.product_code', 'products.name')
            ->orderBy('transaction_count', 'desc')
            ->limit(5)
            ->get();

        // ========== FILTER TEMPORAL ==========
        $filterType = $request->input('filter_type', 'daily');
        $customStartDate = $request->input('custom_start_date');
        $customEndDate = $request->input('custom_end_date');
        list($startDate, $endDate) = $this->getDateRange($filterType, $customStartDate, $customEndDate);

        // ========== BARANG STOK HABIS (DENGAN FILTER) ==========
        $lowStockQuery = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.product_code', 'products.name', 'products.stock', 'products.min_stock_threshold', 'categories.name as category_name')
            ->where('products.is_deleted', 0)
            ->whereRaw('products.stock <= products.min_stock_threshold')
            ->where('products.stock', '>', 0)
            ->orderBy('products.stock', 'asc');

        if ($startDate && $endDate) {
            $lowStockQuery->whereExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('stock_transactions')
                    ->whereColumn('stock_transactions.product_id', 'products.id')
                    ->whereBetween('stock_transactions.transaction_date', [$startDate, $endDate]);
            });
        }
        $lowStockProducts = $lowStockQuery->limit(10)->get();

        // ========== BARANG RUSAK (DENGAN FILTER) ==========
        $damagedQuery = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.product_code',
                'products.name',
                'stock_transactions.quantity as damaged_quantity',
                'stock_transactions.description',
                'stock_transactions.transaction_date',
                'categories.name as category_name'
            )
            ->where('stock_transactions.condition', 'damaged');

        if ($startDate && $endDate) {
            $damagedQuery->whereBetween('stock_transactions.transaction_date', [$startDate, $endDate]);
        }

        $damagedStockProducts = $damagedQuery->orderBy('stock_transactions.transaction_date', 'desc')->limit(10)->get();

        return view('contents.dashboard', compact(
            'role',
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'totalStock',
            'months',
            'inData',
            'outData',
            'userStats',
            'rankingProducts',
            'lowStockProducts',
            'damagedStockProducts',
            'filterType',
            'customStartDate',
            'customEndDate'
        ));
    }

    private function getDateRange($filterType, $customStartDate, $customEndDate)
    {
        $startDate = null;
        $endDate = null;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        switch ($filterType) {
            case 'daily':
                $startDate = $today . ' 00:00:00';
                $endDate = $now;
                break;
            case 'weekly':
                $startDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $endDate = $now;
                break;
            case 'monthly':
                $startDate = date('Y-m-01 00:00:00');
                $endDate = $now;
                break;
            case 'custom':
                if ($customStartDate && $customEndDate) {
                    $startDate = $customStartDate . ' 00:00:00';
                    $endDate = $customEndDate . ' 23:59:59';
                }
                break;
        }

        return [$startDate, $endDate];
    }
}