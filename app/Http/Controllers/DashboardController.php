<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        if (in_array($role, ['admin', 'super_admin'])) {
            $totalUsers = DB::table('users')->count();
        }

        // ========== CHART PRODUK (Jan 2026 - Des 2026) ==========
        $months = ['Jan 2026', 'Feb 2026', 'Mar 2026', 'Apr 2026', 'Mei 2026', 'Jun 2026', 'Jul 2026', 'Agu 2026', 'Sep 2026', 'Okt 2026', 'Nov 2026', 'Des 2026'];
        $inData = [];
        $outData = [];
        
        foreach ($months as $index => $monthName) {
            $year = 2026;
            $month = $index + 1;
            $monthStart = date('Y-m-01', strtotime("$year-$month-01"));
            $monthEnd = date('Y-m-t', strtotime("$year-$month-01"));
            
            $inData[] = DB::table('stock_transactions')
                ->where('type', 'in')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('quantity') ?? 0;
                
            $outData[] = DB::table('stock_transactions')
                ->where('type', 'out')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('quantity') ?? 0;
        }

        // ========== RANKING BARANG (Initial Data) ==========
        $rankingProducts = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select('products.id', 'products.product_code', 'products.name', DB::raw('COUNT(*) as transaction_count'))
            ->where('products.is_deleted', 0)
            ->groupBy('products.id', 'products.product_code', 'products.name')
            ->orderBy('transaction_count', 'desc')
            ->limit(5)
            ->get();

        // ========== USER STATS (Initial Data) ==========
        $userStats = collect([]);
        if (in_array($role, ['admin', 'super_admin'])) {
            $userStats = DB::table('users')
                ->selectRaw("DATE_FORMAT(created_at, '%m') as month, COUNT(*) as total")
                ->whereNotNull('created_at')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // ========== BARANG STOK HABIS (TANPA FILTER) ==========
        $lowStockProducts = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.product_code', 'products.name', 'products.stock', 'products.min_stock_threshold', 'categories.name as category_name')
            ->where('products.is_deleted', 0)
            ->whereRaw('products.stock <= products.min_stock_threshold')
            ->where('products.stock', '>', 0)
            ->orderBy('products.stock', 'asc')
            ->limit(10)
            ->get();

        // ========== BARANG RUSAK (DENGAN FILTER - DEFAULT CUSTOM) ==========
        $filterType = $request->input('filter_type', 'custom'); // DEFAULT CUSTOM
        $customStartDate = $request->input('custom_start_date');
        $customEndDate = $request->input('custom_end_date');
        list($startDate, $endDate) = $this->getDateRange($filterType, $customStartDate, $customEndDate);

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
            'rankingProducts',
            'userStats',
            'lowStockProducts',
            'damagedStockProducts',
            'filterType',
            'customStartDate',
            'customEndDate'
        ));
    }

    /**
     * API endpoint untuk chart data (AJAX)
     */
    public function getChartData(Request $request)
    {
        $chartType = $request->input('chart_type');
        $filterType = $request->input('filter_type', 'all');
        $customStartDate = $request->input('custom_start_date');
        $customEndDate = $request->input('custom_end_date');
        
        $data = [];
        
        switch ($chartType) {
            case 'product':
                $data = $this->getProductChartData($filterType, $customStartDate, $customEndDate);
                break;
            case 'ranking':
                $data = $this->getRankingChartData($filterType, $customStartDate, $customEndDate);
                break;
            case 'user':
                $data = $this->getUserChartData($filterType, $customStartDate, $customEndDate);
                break;
        }
        
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * API endpoint untuk damaged stock data (AJAX)
     */
    public function getDamagedStockData(Request $request)
    {
        $filterType = $request->input('filter_type', 'custom');
        $customStartDate = $request->input('custom_start_date');
        $customEndDate = $request->input('custom_end_date');
        
        list($startDate, $endDate) = $this->getDateRange($filterType, $customStartDate, $customEndDate);
        
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
        
        return response()->json([
            'success' => true,
            'data' => $damagedStockProducts->map(function($item) {
                return [
                    'transaction_date' => Carbon::parse($item->transaction_date)->translatedFormat('d F Y'),
                    'product_code' => $item->product_code,
                    'name' => $item->name,
                    'damaged_quantity' => number_format($item->damaged_quantity),
                    'description' => $item->description ?? '-',
                    'category_name' => $item->category_name
                ];
            })
        ]);
    }

    private function getProductChartData($filterType, $customStartDate, $customEndDate)
{
    setlocale(LC_TIME, 'id_ID');
    
    $inData = [];
    $outData = [];
    $labels = [];
    
    // Jika all atau custom dengan tanggal kosong -> tampilkan per bulan (all time)
    if ($filterType === 'all' || ($filterType === 'custom' && !$customStartDate && !$customEndDate)) {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $inData = array_fill(0, 12, 0);
        $outData = array_fill(0, 12, 0);
        
        $inResults = DB::table('stock_transactions')
            ->where('type', 'in')
            ->selectRaw('MONTH(transaction_date) as month, SUM(quantity) as total')
            ->whereNotNull('transaction_date')
            ->groupBy('month')
            ->get();
            
        $outResults = DB::table('stock_transactions')
            ->where('type', 'out')
            ->selectRaw('MONTH(transaction_date) as month, SUM(quantity) as total')
            ->whereNotNull('transaction_date')
            ->groupBy('month')
            ->get();
            
        foreach ($inResults as $r) {
            $idx = ($r->month - 1);
            if ($idx >= 0 && $idx < 12) $inData[$idx] = $r->total;
        }
        foreach ($outResults as $r) {
            $idx = ($r->month - 1);
            if ($idx >= 0 && $idx < 12) $outData[$idx] = $r->total;
        }
        
        return [
            'labels' => $months,
            'inData' => array_values($inData),
            'outData' => array_values($outData),
            'type' => 'monthly'
        ];
    } 
    // Filter dengan custom date range
    elseif ($customStartDate && $customEndDate) {
        $startDate = $customStartDate . ' 00:00:00';
        $endDate = $customEndDate . ' 23:59:59';
        $diffInDays = (strtotime($customEndDate) - strtotime($customStartDate)) / 86400 + 1;
        
        // Jika rentang waktu <= 31 hari -> tampilkan harian
        if ($diffInDays <= 31) {
            $results = DB::table('stock_transactions')
                ->selectRaw('DATE(transaction_date) as date, type, SUM(quantity) as total')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date', 'type')
                ->orderBy('date', 'asc')
                ->get();
                
            $dateRange = [];
            $current = strtotime($customStartDate);
            $end = strtotime($customEndDate);
            while ($current <= $end) {
                $dayNum = date('j', $current);
                $monthNum = date('n', $current);
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $dateRange[date('Y-m-d', $current)] = $dayNum . ' ' . $monthName;
                $current = strtotime('+1 day', $current);
            }
            
            $inData = array_fill(0, count($dateRange), 0);
            $outData = array_fill(0, count($dateRange), 0);
            $labels = array_values($dateRange);
            $dateKeys = array_keys($dateRange);
            
            foreach ($results as $r) {
                $idx = array_search($r->date, $dateKeys);
                if ($idx !== false) {
                    if ($r->type == 'in') {
                        $inData[$idx] = $r->total;
                    } else {
                        $outData[$idx] = $r->total;
                    }
                }
            }
            
            return [
                'labels' => $labels,
                'inData' => $inData,
                'outData' => $outData,
                'type' => 'daily'
            ];
        } 
        // Jika rentang waktu > 31 hari -> tampilkan bulanan
        else {
            $results = DB::table('stock_transactions')
                ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, type, SUM(quantity) as total')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('month', 'type')
                ->orderBy('month', 'asc')
                ->get();
                
            $monthRange = [];
            $current = strtotime($customStartDate);
            $end = strtotime($customEndDate);
            while ($current <= $end) {
                $monthYear = date('Y-m', $current);
                $monthNum = date('n', $current);
                $yearNum = date('Y', $current);
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $monthRange[$monthYear] = $monthName . ' ' . $yearNum;
                $current = strtotime('first day of next month', $current);
            }
            
            $inData = array_fill(0, count($monthRange), 0);
            $outData = array_fill(0, count($monthRange), 0);
            $labels = array_values($monthRange);
            $monthKeys = array_keys($monthRange);
            
            foreach ($results as $r) {
                $idx = array_search($r->month, $monthKeys);
                if ($idx !== false) {
                    if ($r->type == 'in') {
                        $inData[$idx] = $r->total;
                    } else {
                        $outData[$idx] = $r->total;
                    }
                }
            }
            
            return [
                'labels' => $labels,
                'inData' => $inData,
                'outData' => $outData,
                'type' => 'monthly'
            ];
        }
    }
    // Filter Harian, Mingguan, Bulanan (tanpa custom date)
    elseif ($filterType !== 'all' && $filterType !== 'custom') {
        list($startDate, $endDate) = $this->getDateRangeForChart($filterType, null, null);
        
        if ($filterType == 'daily') {
            // Harian: tampilkan tanggal
            $results = DB::table('stock_transactions')
                ->selectRaw('DATE(transaction_date) as date, type, SUM(quantity) as total')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date', 'type')
                ->orderBy('date', 'asc')
                ->get();
                
            $inData = [];
            $outData = [];
            $labels = [];
            
            foreach ($results as $r) {
                $dayNum = date('j', strtotime($r->date));
                $monthNum = date('n', strtotime($r->date));
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $dateLabel = $dayNum . ' ' . $monthName;
                
                if (!in_array($dateLabel, $labels)) {
                    $labels[] = $dateLabel;
                    $inData[] = 0;
                    $outData[] = 0;
                }
                $idx = array_search($dateLabel, $labels);
                if ($r->type == 'in') {
                    $inData[$idx] = $r->total;
                } else {
                    $outData[$idx] = $r->total;
                }
            }
            
            return [
                'labels' => $labels,
                'inData' => $inData,
                'outData' => $outData,
                'type' => 'daily'
            ];
        } 
        elseif ($filterType == 'weekly') {
            // Mingguan: tampilkan SEMUA tanggal dalam 7 hari terakhir
            $results = DB::table('stock_transactions')
                ->selectRaw('DATE(transaction_date) as date, type, SUM(quantity) as total')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date', 'type')
                ->orderBy('date', 'asc')
                ->get();
            
            // Buat date range untuk 7 hari
            $dateRange = [];
            $current = strtotime($startDate);
            $end = strtotime($endDate);
            
            while ($current <= $end) {
                $dayNum = date('j', $current);
                $monthNum = date('n', $current);
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $dateLabel = $dayNum . ' ' . $monthName;
                $dateKey = date('Y-m-d', $current);
                
                $dateRange[$dateKey] = $dateLabel;
                $current = strtotime('+1 day', $current);
            }
            
            // Inisialisasi data dengan 0 untuk semua tanggal
            $inData = array_fill(0, count($dateRange), 0);
            $outData = array_fill(0, count($dateRange), 0);
            $labels = array_values($dateRange);
            $dateKeys = array_keys($dateRange);
            
            // Map data hasil query
            foreach ($results as $r) {
                $idx = array_search($r->date, $dateKeys);
                if ($idx !== false) {
                    if ($r->type == 'in') {
                        $inData[$idx] = $r->total;
                    } else {
                        $outData[$idx] = $r->total;
                    }
                }
            }
            
            return [
                'labels' => $labels,
                'inData' => $inData,
                'outData' => $outData,
                'type' => 'daily'
            ];
        } 
        elseif ($filterType == 'monthly') {
            // Bulanan: tampilkan nama bulan
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $inData = array_fill(0, 12, 0);
            $outData = array_fill(0, 12, 0);
            
            $results = DB::table('stock_transactions')
                ->selectRaw('MONTH(transaction_date) as month, type, SUM(quantity) as total')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('month', 'type')
                ->orderBy('month', 'asc')
                ->get();
                
            foreach ($results as $r) {
                $idx = ($r->month - 1);
                if ($idx >= 0 && $idx < 12) {
                    if ($r->type == 'in') {
                        $inData[$idx] = $r->total;
                    } else {
                        $outData[$idx] = $r->total;
                    }
                }
            }
            
            // Hanya tampilkan bulan yang memiliki data atau yang masuk rentang
            $nonZeroIndices = [];
            for ($i = 0; $i < 12; $i++) {
                if ($inData[$i] > 0 || $outData[$i] > 0) {
                    $nonZeroIndices[] = $i;
                }
            }
            
            if (count($nonZeroIndices) > 0) {
                $filteredLabels = [];
                $filteredInData = [];
                $filteredOutData = [];
                foreach ($nonZeroIndices as $idx) {
                    $filteredLabels[] = $months[$idx];
                    $filteredInData[] = $inData[$idx];
                    $filteredOutData[] = $outData[$idx];
                }
                return [
                    'labels' => $filteredLabels,
                    'inData' => $filteredInData,
                    'outData' => $filteredOutData,
                    'type' => 'monthly'
                ];
            }
            
            return [
                'labels' => $months,
                'inData' => $inData,
                'outData' => $outData,
                'type' => 'monthly'
            ];
        }
    }
    
    // Default return
    return [
        'labels' => [],
        'inData' => [],
        'outData' => [],
        'type' => 'monthly'
    ];
}
    
    private function getRankingChartData($filterType, $customStartDate, $customEndDate)
    {
        $query = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select('products.id', 'products.product_code', 'products.name', DB::raw('COUNT(*) as transaction_count'))
            ->where('products.is_deleted', 0)
            ->groupBy('products.id', 'products.product_code', 'products.name');
        
        // Jika all atau custom dengan tanggal kosong -> tampilkan all time
        if ($filterType === 'all' || ($filterType === 'custom' && !$customStartDate && !$customEndDate)) {
            // No date filter
        } 
        elseif ($customStartDate && $customEndDate) {
            $startDate = $customStartDate . ' 00:00:00';
            $endDate = $customEndDate . ' 23:59:59';
            $query->whereBetween('stock_transactions.transaction_date', [$startDate, $endDate]);
        }
        elseif ($filterType !== 'all' && $filterType !== 'custom') {
            list($startDate, $endDate) = $this->getDateRangeForChart($filterType, null, null);
            if ($startDate && $endDate) {
                $query->whereBetween('stock_transactions.transaction_date', [$startDate, $endDate]);
            }
        }
        
        $ranking = $query->orderBy('transaction_count', 'desc')->limit(5)->get();
        
        return [
            'labels' => $ranking->map(function($item) {
                return strlen($item->name) > 15 ? substr($item->name, 0, 12) . '...' : $item->name;
            }),
            'data' => $ranking->pluck('transaction_count')
        ];
    }
    
    private function getUserChartData($filterType, $customStartDate, $customEndDate)
{
    setlocale(LC_TIME, 'id_ID');
    
    $labels = [];
    $userData = [];
    
    // Jika all atau custom dengan tanggal kosong -> tampilkan per bulan (all time)
    if ($filterType === 'all' || ($filterType === 'custom' && !$customStartDate && !$customEndDate)) {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $userData = array_fill(0, 12, 0);
        
        $results = DB::table('users')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereNotNull('created_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        foreach ($results as $r) {
            $idx = ($r->month - 1);
            if ($idx >= 0 && $idx < 12) $userData[$idx] = $r->total;
        }
        
        return [
            'labels' => $months,
            'data' => $userData,
            'type' => 'monthly'
        ];
    }
    
    // Filter dengan custom date range
    elseif ($customStartDate && $customEndDate) {
        $startDate = $customStartDate . ' 00:00:00';
        $endDate = $customEndDate . ' 23:59:59';
        $diffInDays = (strtotime($customEndDate) - strtotime($customStartDate)) / 86400 + 1;
        
        // Jika rentang waktu <= 31 hari -> tampilkan harian
        if ($diffInDays <= 31) {
            $results = DB::table('users')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                
            $dateRange = [];
            $current = strtotime($customStartDate);
            $end = strtotime($customEndDate);
            while ($current <= $end) {
                $dayNum = date('j', $current);
                $monthNum = date('n', $current);
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $dateRange[date('Y-m-d', $current)] = $dayNum . ' ' . $monthName;
                $current = strtotime('+1 day', $current);
            }
            
            $userData = array_fill(0, count($dateRange), 0);
            $labels = array_values($dateRange);
            $dateKeys = array_keys($dateRange);
            
            foreach ($results as $r) {
                $idx = array_search($r->date, $dateKeys);
                if ($idx !== false) {
                    $userData[$idx] = $r->total;
                }
            }
            
            return [
                'labels' => $labels,
                'data' => $userData,
                'type' => 'daily'
            ];
        }
        // Jika rentang waktu > 31 hari -> tampilkan bulanan
        else {
            $results = DB::table('users')
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
                
            $monthRange = [];
            $current = strtotime($customStartDate);
            $end = strtotime($customEndDate);
            while ($current <= $end) {
                $monthYear = date('Y-m', $current);
                $monthNum = date('n', $current);
                $yearNum = date('Y', $current);
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $monthRange[$monthYear] = $monthName . ' ' . $yearNum;
                $current = strtotime('first day of next month', $current);
            }
            
            $userData = array_fill(0, count($monthRange), 0);
            $labels = array_values($monthRange);
            $monthKeys = array_keys($monthRange);
            
            foreach ($results as $r) {
                $idx = array_search($r->month, $monthKeys);
                if ($idx !== false) {
                    $userData[$idx] = $r->total;
                }
            }
            
            return [
                'labels' => $labels,
                'data' => $userData,
                'type' => 'monthly'
            ];
        }
    }
    // Filter Harian, Mingguan, Bulanan (tanpa custom date)
    elseif ($filterType !== 'all' && $filterType !== 'custom') {
        list($startDate, $endDate) = $this->getDateRangeForChart($filterType, null, null);
        
        if ($filterType == 'daily') {
            // Harian: tampilkan tanggal
            $results = DB::table('users')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                
            $userData = [];
            $labels = [];
            
            foreach ($results as $r) {
                $dayNum = date('j', strtotime($r->date));
                $monthNum = date('n', strtotime($r->date));
                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthName = $monthNames[$monthNum];
                $dateLabel = $dayNum . ' ' . $monthName;
                
                if (!in_array($dateLabel, $labels)) {
                    $labels[] = $dateLabel;
                    $userData[] = 0;
                }
                $idx = array_search($dateLabel, $labels);
                $userData[$idx] = $r->total;
            }
            
            return [
                'labels' => $labels,
                'data' => $userData,
                'type' => 'daily'
            ];
        }
        elseif ($filterType == 'weekly') {
    // Mingguan: tampilkan SEMUA tanggal dalam 7 hari terakhir
    $results = DB::table('users')
        ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
    
    // Buat date range untuk 7 hari
    $dateRange = [];
    $current = strtotime($startDate);
    $end = strtotime($endDate);
    
    while ($current <= $end) {
        $dayNum = date('j', $current);
        $monthNum = date('n', $current);
        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthName = $monthNames[$monthNum];
        $dateLabel = $dayNum . ' ' . $monthName;
        $dateKey = date('Y-m-d', $current);
        
        $dateRange[$dateKey] = $dateLabel;
        $current = strtotime('+1 day', $current);
    }
    
    $userData = array_fill(0, count($dateRange), 0);
    $labels = array_values($dateRange);
    $dateKeys = array_keys($dateRange);
    
    $dataMap = [];
    foreach ($results as $r) {
        $dataMap[$r->date] = $r->total;
    }
    
    foreach ($dateKeys as $idx => $dateKey) {
        $userData[$idx] = $dataMap[$dateKey] ?? 0;
    }
    
    return [
        'labels' => $labels,
        'data' => $userData,
        'type' => 'daily'
    ];
}
        elseif ($filterType == 'monthly') {
            // Bulanan: tampilkan nama bulan
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $userData = array_fill(0, 12, 0);
            
            $results = DB::table('users')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                
            foreach ($results as $r) {
                $idx = ($r->month - 1);
                if ($idx >= 0 && $idx < 12) {
                    $userData[$idx] = $r->total;
                }
            }
            
            // Hanya tampilkan bulan yang memiliki data
            $nonZeroIndices = [];
            for ($i = 0; $i < 12; $i++) {
                if ($userData[$i] > 0) {
                    $nonZeroIndices[] = $i;
                }
            }
            
            if (count($nonZeroIndices) > 0) {
                $filteredLabels = [];
                $filteredData = [];
                foreach ($nonZeroIndices as $idx) {
                    $filteredLabels[] = $months[$idx];
                    $filteredData[] = $userData[$idx];
                }
                return [
                    'labels' => $filteredLabels,
                    'data' => $filteredData,
                    'type' => 'monthly'
                ];
            }
            
            return [
                'labels' => $months,
                'data' => $userData,
                'type' => 'monthly'
            ];
        }
    }
    
    // Default return
    return [
        'labels' => [],
        'data' => [],
        'type' => 'monthly'
    ];
}
    
    private function getDateRangeForChart($filterType, $customStartDate, $customEndDate)
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
            // 7 hari terakhir
            $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
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
                // Jika tanggal kosong, $startDate dan $endDate tetap null -> all-time
                break;
        }

        return [$startDate, $endDate];
    }
}