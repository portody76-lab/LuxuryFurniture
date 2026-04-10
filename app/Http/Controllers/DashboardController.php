<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // ========== DASHBOARD ADMIN (Lengkap dengan data user) ==========
    public function adminDashboard()
    {
        // Total Produk
        $totalProducts = DB::table('products')->where('is_deleted', 0)->count();
        
        // Total Kategori
        $totalCategories = DB::table('categories')->count();
        
        // Total Users
        $totalUsers = DB::table('users')->count();

        // Total Stok
        $totalStock = DB::table('stock_transactions')
            ->selectRaw("
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) -
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END)
                AS total_stock
            ")
            ->value('total_stock');

        // Chart Produk (berdasarkan stock_transactions)
        $productStats = DB::table('stock_transactions')
            ->selectRaw("
                DATE_FORMAT(transaction_date, '%m') as month,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Chart Users (berdasarkan created_at)
        $userStats = DB::table('users')
            ->selectRaw("
                DATE_FORMAT(created_at, '%m') as month,
                COUNT(*) as total
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Distribusi kategori per produk
        $categoryStats = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(products.id) as total'))
            ->where('products.is_deleted', 0)
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

    // ========== DASHBOARD OPERATOR (TETAP KIRIM SEMUA DATA, VIEW YANG MENGATUR) ==========
    public function operatorDashboard()
    {
        // Total Produk
        $totalProducts = DB::table('products')->where('is_deleted', 0)->count();
        
        // Total Kategori
        $totalCategories = DB::table('categories')->count();
        
        // Total Users (tetap diambil, tapi nanti di view disembunyikan untuk operator)
        $totalUsers = DB::table('users')->count();

        // Total Stok
        $totalStock = DB::table('stock_transactions')
            ->selectRaw("
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) -
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END)
                AS total_stock
            ")
            ->value('total_stock');

        // Chart Produk (berdasarkan stock_transactions)
        $productStats = DB::table('stock_transactions')
            ->selectRaw("
                DATE_FORMAT(transaction_date, '%m') as month,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Chart Users (tetap diambil, tapi nanti di view disembunyikan untuk operator)
        $userStats = DB::table('users')
            ->selectRaw("
                DATE_FORMAT(created_at, '%m') as month,
                COUNT(*) as total
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Distribusi kategori per produk
        $categoryStats = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(products.id) as total'))
            ->where('products.is_deleted', 0)
            ->groupBy('categories.name')
            ->get();

        // KIRIM SEMUA DATA (view yang akan menyembunyikan elemen user)
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