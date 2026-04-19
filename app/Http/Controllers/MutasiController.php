<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MutasiController extends Controller
{
    /**
     * Display a listing of stock mutations with filters
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->role_name ?? 'operator';

        // Get data for filters
        $products = DB::table('products')
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get(['id', 'product_code', 'name']);

        $categories = DB::table('categories')
            ->orderBy('name')
            ->get(['id', 'name']);

        // Build query
        $query = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('users', 'stock_transactions.created_by', '=', 'users.id')
            ->select(
                'stock_transactions.id',
                'stock_transactions.transaction_date',
                'products.product_code',
                'products.name as product_name',
                'categories.name as category_name',
                'stock_transactions.type',
                'stock_transactions.quantity',
                'stock_transactions.condition',
                'stock_transactions.description',
                'users.username as user_name'
            )
            ->orderBy('stock_transactions.transaction_date', 'desc');

        // Apply filters
        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('stock_transactions.product_id', $request->product_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->category_id);
        }

        // Filter by date range
        $filterType = $request->input('filter_type', 'daily');
        $customStartDate = $request->input('custom_start_date');
        $customEndDate = $request->input('custom_end_date');
        
        // PRIORITAS: Jika custom date diisi, gunakan custom date
        if ($customStartDate && $customEndDate) {
            $startDate = $customStartDate . ' 00:00:00';
            $endDate = $customEndDate . ' 23:59:59';
            $query->whereBetween('stock_transactions.transaction_date', [$startDate, $endDate]);
            $filterType = 'custom';
        } else {
            // Gunakan filter periode (harian, mingguan, bulanan)
            list($startDate, $endDate) = $this->getDateRange($filterType);
            if ($startDate && $endDate) {
                $query->whereBetween('stock_transactions.transaction_date', [$startDate, $endDate]);
            }
        }

        // Get paginated results
        $transactions = $query->paginate(20);

        // Format tanggal untuk ditampilkan di view
        foreach ($transactions as $transaction) {
            $transaction->formatted_date = \Carbon\Carbon::parse($transaction->transaction_date)
                ->translatedFormat('d F Y H:i') . ' WIB';
        }

        // Keep filter values for view
        $selectedProductId = $request->input('product_id');
        $selectedCategoryId = $request->input('category_id');
        $selectedFilterType = $filterType;
        $selectedCustomStartDate = $customStartDate;
        $selectedCustomEndDate = $customEndDate;

        return view('contents.mutasi', compact(
            'transactions',
            'products',
            'categories',
            'selectedProductId',
            'selectedCategoryId',
            'selectedFilterType',
            'selectedCustomStartDate',
            'selectedCustomEndDate',
            'role'
        ));
    }

    /**
     * Get date range based on filter type
     */
    private function getDateRange($filterType)
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
        }

        return [$startDate, $endDate];
    }
}