<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk semua role (admin, super_admin, operator)
     * Data yang ditampilkan akan berbeda sesuai role di view
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role_name ?? 'operator';

        // ========== DATA UMUM UNTUK SEMUA ROLE ==========
        
        // Total Produk
        $totalProducts = DB::table('products')->where('is_deleted', 0)->count();
        
        // Total Kategori
        $totalCategories = DB::table('categories')->count();
        
        // Total Stok
        $totalStock = DB::table('stock_transactions')
            ->selectRaw("
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) -
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END)
                AS total_stock
            ")
            ->value('total_stock') ?? 0;

        // ========== DATA KHUSUS UNTUK ADMIN & SUPER ADMIN ==========
        $totalUsers = 0;
        $userStats = collect([]);
        
        if (in_array($role, ['admin', 'super_admin'])) {
            // Total Users (hanya untuk admin & super_admin)
            $totalUsers = DB::table('users')->count();
            
            // Chart Users (berdasarkan created_at)
            $userStats = DB::table('users')
                ->selectRaw("
                    DATE_FORMAT(created_at, '%m') as month,
                    COUNT(*) as total
                ")
                ->whereNotNull('created_at')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // ========== DATA UNTUK SEMUA ROLE (LANJUTAN) ==========
        
        // Chart Produk (barang keluar per bulan)
        $productStats = DB::table('stock_transactions')
            ->selectRaw("
                DATE_FORMAT(transaction_date, '%m') as month,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total
            ")
            ->whereNotNull('transaction_date')
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

        // ========== BARANG YANG STOKNYA AKAN HABIS (<= min_stock_threshold) ==========
        $lowStockProducts = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.product_code', 'products.name', 'products.stock', 'products.min_stock_threshold', 'categories.name as category_name')
            ->where('products.is_deleted', 0)
            ->whereRaw('products.stock <= products.min_stock_threshold')
            ->where('products.stock', '>', 0)
            ->orderBy('products.stock', 'asc')
            ->limit(10)
            ->get();

        // ========== BARANG DENGAN STOK RUSAK ==========
        $damagedStockProducts = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.product_code', 'products.name', 'stock_transactions.quantity as damaged_quantity', 'stock_transactions.damage_reason', 'stock_transactions.transaction_date', 'categories.name as category_name')
            ->where('stock_transactions.condition', 'damaged')
            ->where('stock_transactions.type', 'out')
            ->orderBy('stock_transactions.transaction_date', 'desc')
            ->limit(10)
            ->get();

        // ========== RANKING BARANG (Paling Sering Keluar-Masuk) ==========
        $rankingProducts = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select('products.id', 'products.product_code', 'products.name', DB::raw('COUNT(*) as transaction_count'))
            ->where('products.is_deleted', 0)
            ->groupBy('products.id', 'products.product_code', 'products.name')
            ->orderBy('transaction_count', 'desc')
            ->limit(5)
            ->get();

        return view('contents.dashboard', compact(
            'role',
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'totalStock',
            'productStats',
            'userStats',
            'categoryStats',
            'lowStockProducts',
            'damagedStockProducts',
            'rankingProducts'
        ));
    }
}