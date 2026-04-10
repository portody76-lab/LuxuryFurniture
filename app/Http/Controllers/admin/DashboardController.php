<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = DB::table('products')->where('is_deleted', 0)->count();
        $totalCategories = DB::table('categories')->count();
        $totalUsers = DB::table('users')->count();

        $totalStock = DB::table('stock_transactions')
            ->selectRaw("
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) -
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END)
                AS total_stock
            ")
            ->value('total_stock');

        // Chart Produk
        $productStats = DB::table('stock_transactions')
            ->selectRaw("
        DATE_FORMAT(transaction_date, '%m') as month,
        SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total
    ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Chart Users
        $userStats = DB::table('users')
            ->selectRaw("
        DATE_FORMAT(created_at, '%m') as month,
        COUNT(*) as total
    ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Distribusi kategori
        $categoryStats = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(products.id) as total'))
            ->groupBy('categories.name')
            ->get();

        return view('contents.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'totalStock',
            'productStats',
            'userStats',
            'categoryStats'
        ));
    }
}
