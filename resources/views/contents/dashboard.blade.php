@extends('layout.content')

@section('title', ucfirst(auth()->user()->role->role_name) . ' Dashboard - Luxury Furniture')

@section('content')
    <div class="bg-white p-5 sm:p-8 rounded-2xl mb-6 sm:mb-8 shadow-md border border-[#e7ddcf]">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800">
            Halo, {{ auth()->user()->username }}
        </h2>
        <p class="text-[#8b7a66] text-sm sm:text-base mt-2">
            Selamat datang di Dashboard {{ ucfirst(auth()->user()->role->role_name) }} Luxury Furniture
        </p>
    </div>

    <!-- Cards - Semua Role (Bisa Diklik) -->
    @php
        $cards = [
            ['title' => 'Total Produk', 'value' => number_format($totalProducts), 'icon' => 'fa-couch', 'color' => '#b68b40', 'route' => 'contents.productmanage'],
            ['title' => 'Total Kategori', 'value' => number_format($totalCategories), 'icon' => 'fa-layer-group', 'color' => '#8faa7b', 'route' => 'contents.categories'],
            ['title' => 'Total Stok', 'value' => number_format($totalStock ?? 0), 'icon' => 'fa-boxes', 'color' => '#ab8e64', 'route' => 'contents.stockmanage'],
        ];
        
        if (in_array($role, ['admin', 'super_admin'])) {
            $cards[] = ['title' => 'Total Users', 'value' => number_format($totalUsers), 'icon' => 'fa-users', 'color' => '#c9a87b', 'route' => 'contents.users'];
        }
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 {{ count($cards) == 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }} gap-4 sm:gap-6 mb-8 sm:mb-10">
    @foreach($cards as $card)
        <a href="{{ route($card['route']) }}" 
           class="block bg-white rounded-2xl shadow-md p-4 sm:p-6 flex items-center justify-between border-l-4 border-l-[{{ $card['color'] }}] hover:shadow-lg transition-all duration-200 hover:-translate-y-1 cursor-pointer">
            <div>
                <p class="text-gray-400 text-xs sm:text-sm uppercase tracking-wider mb-1">{{ $card['title'] }}</p>
                <p class="text-2xl sm:text-4xl font-bold text-gray-800">{{ $card['value'] }}</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-[{{ $card['color'] }}]/10 rounded-full flex items-center justify-center shrink-0">
                <i class="fas {{ $card['icon'] }} text-lg sm:text-2xl" style="color: {{ $card['color'] }}"></i>
            </div>
        </a>
    @endforeach
</div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8 sm:mb-10">
        
        <!-- Chart Produk (Barang Masuk & Keluar) -->
        <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-4 sm:mb-5 pb-3 border-b border-gray-100">
                <i class="fas fa-chart-bar text-[#b68b40] text-lg sm:text-xl"></i>
                <h4 class="font-semibold text-gray-800 text-base sm:text-lg">Statistik Transaksi Produk (12 Bulan Terakhir)</h4>
            </div>

            @if(array_sum($inData) > 0 || array_sum($outData) > 0)
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="productChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-chart-line"></i></div>
                    <h3 class="empty-state-title">Belum Ada Data Transaksi</h3>
                    <p class="empty-state-desc">Belum ada transaksi yang tercatat</p>
                </div>
            @endif
        </div>

        <!-- Ranking Barang (Vertical Bar Chart) -->
        <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-4 sm:mb-5 pb-3 border-b border-gray-100">
                <i class="fas fa-chart-simple text-[#c9973a] text-lg sm:text-xl"></i>
                <h4 class="font-semibold text-gray-800 text-base sm:text-lg">🏆 Top 5 Barang Paling Aktif</h4>
            </div>

            @if(isset($rankingProducts) && count($rankingProducts) > 0)
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="rankingChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-chart-simple"></i></div>
                    <h3 class="empty-state-title">Belum Ada Data Transaksi</h3>
                    <p class="empty-state-desc">Belum ada transaksi yang tercatat</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Chart Users (Hanya untuk Admin & Super Admin) -->
    @if(in_array($role, ['admin', 'super_admin']))
        <div class="grid grid-cols-1 gap-4 sm:gap-6 mb-8 sm:mb-10">
            <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-4 sm:mb-5 pb-3 border-b border-gray-100">
                    <i class="fas fa-chart-line text-[#8faa7b] text-lg sm:text-xl"></i>
                    <h4 class="font-semibold text-gray-800 text-base sm:text-lg">Statistik Pertumbuhan User</h4>
                </div>

                @if(isset($userStats) && count($userStats) > 0 && $userStats->sum('total') > 0)
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="userChart"></canvas>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-users"></i></div>
                        <h3 class="empty-state-title">Belum Ada Data User</h3>
                        <p class="empty-state-desc">Belum ada user yang terdaftar</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- BAGIAN BAWAH: Barang Stok Habis & Barang Rusak dengan Filter -->
    <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-[#c9973a]"></i> Manajemen Stok & Kerusakan
            </h3>

            <!-- Filter Temporal -->
            <form method="GET" action="{{ route('contents.dashboard') }}" class="flex flex-wrap items-center gap-2">
                <select name="filter_type" id="filter_type" class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:border-[#c9973a] focus:outline-none">
                    <option value="daily" {{ $filterType == 'daily' ? 'selected' : '' }}>📅 Harian</option>
                    <option value="weekly" {{ $filterType == 'weekly' ? 'selected' : '' }}>📆 Mingguan</option>
                    <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>📅 Bulanan</option>
                    <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>📅 Custom</option>
                </select>
                
                <div id="customDateRange" class="flex gap-2 {{ $filterType == 'custom' ? '' : 'hidden' }}">
                    <input type="date" name="custom_start_date" value="{{ $customStartDate }}" class="border border-gray-300 rounded-xl px-3 py-2 text-sm">
                    <span class="text-gray-500">-</span>
                    <input type="date" name="custom_end_date" value="{{ $customEndDate }}" class="border border-gray-300 rounded-xl px-3 py-2 text-sm">
                </div>
                
                <button type="submit" class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-filter"></i> Filter
                </button>
                
                <a href="{{ route('contents.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Barang Stok Akan Habis -->
            <div class="border border-orange-200 rounded-2xl overflow-hidden">
                <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                    <h4 class="font-semibold text-orange-700 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> ⚠️ Barang Stok Akan Habis
                    </h4>
                </div>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Kode</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Nama Barang</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Kategori</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Stok</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Minimal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lowStockProducts as $item)
                                <tr class="hover:bg-orange-50/50 transition">
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $item->product_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $item->category_name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                            {{ number_format($item->stock) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">{{ number_format($item->min_stock_threshold) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                        <i class="fas fa-check-circle text-green-400 text-2xl mb-2 block"></i>
                                        Semua stok aman
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Barang Stok Rusak -->
            <div class="border border-red-200 rounded-2xl overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h4 class="font-semibold text-red-700 flex items-center gap-2">
                        <i class="fas fa-times-circle"></i> ❌ Barang Stok Rusak
                    </h4>
                </div>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Kode</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Nama Barang</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($damagedStockProducts as $item)
                                <tr class="hover:bg-red-50/50 transition">
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ date('d/m/Y', strtotime($item->transaction_date)) }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $item->product_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            {{ number_format($item->damaged_quantity) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 max-w-37.5 truncate" title="{{ $item->damage_reason ?? '-' }}">
                                        {{ $item->description ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                        <i class="fas fa-check-circle text-green-400 text-2xl mb-2 block"></i>
                                        Tidak ada barang rusak
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <style>
        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state-icon { width: 70px; height: 70px; margin: 0 auto 16px; background: linear-gradient(135deg, #f5e6c8 0%, #e8d5a8 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .empty-state-icon i { font-size: 28px; color: #c9973a; }
        .empty-state-title { font-size: 16px; font-weight: 600; color: #5a4a1e; margin-bottom: 8px; }
        .empty-state-desc { font-size: 13px; color: #8b7a66; margin-bottom: 20px; }
        
        /* Sembunyikan scrollbar tapi tetap bisa scroll */
        .overflow-y-auto::-webkit-scrollbar { width: 4px; }
        .overflow-y-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .overflow-y-auto::-webkit-scrollbar-thumb { background: #c9973a; border-radius: 10px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ========== 1. PRODUCT CHART (Barang Masuk & Keluar) ==========
            const months = @json($months);
            const inData = @json($inData);
            const outData = @json($outData);
            
            const productCtx = document.getElementById('productChart')?.getContext('2d');
            if (productCtx && (inData.some(v => v > 0) || outData.some(v => v > 0))) {
                new Chart(productCtx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            { label: '📦 Barang Masuk', data: inData, backgroundColor: '#4caf50', borderRadius: 8, barPercentage: 0.65 },
                            { label: '📤 Barang Keluar', data: outData, backgroundColor: '#f44336', borderRadius: 8, barPercentage: 0.65 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { position: 'top' },
                            tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.raw.toLocaleString() + ' unit'; } } }
                        },
                        scales: { 
                            y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, callback: function(value) { return value.toLocaleString(); } } },
                            x: { ticks: { rotate: 45, maxRotation: 45, minRotation: 45, font: { size: 10 } } }
                        }
                    }
                });
            }

            // ========== 2. RANKING CHART (Vertical Bar Chart - Top 5 Barang Paling Aktif) ==========
            const rankingData = @json($rankingProducts ?? []);
            const rankingCtx = document.getElementById('rankingChart')?.getContext('2d');
            
            if (rankingCtx && rankingData.length > 0) {
                // Potong nama barang jika terlalu panjang
                const labels = rankingData.map(d => {
                    let name = d.name;
                    return name.length > 18 ? name.substring(0, 15) + '...' : name;
                });
                
                new Chart(rankingCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Frekuensi Transaksi',
                            data: rankingData.map(d => d.transaction_count),
                            backgroundColor: ['#c9973a', '#d4a85c', '#e0b86e', '#ecc880', '#f8d892'],
                            borderRadius: 8,
                            barPercentage: 0.7,
                            categoryPercentage: 0.85
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Frekuensi: ' + context.raw + 'x transaksi';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    callback: function(value) {
                                        return value + 'x';
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Jumlah Transaksi',
                                    font: { size: 12 }
                                }
                            },
                            x: {
                                ticks: {
                                    font: { size: 11 },
                                    rotation: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Nama Barang',
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            }

            // ========== 3. USER CHART (Hanya untuk Admin & Super Admin) ==========
            @if(in_array($role, ['admin', 'super_admin']))
                const userData = @json($userStats ?? []);
                const userCtx = document.getElementById('userChart')?.getContext('2d');
                if (userCtx && userData.length > 0) {
                    const monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const userValues = Array(12).fill(0);
                    userData.forEach(d => { if (d.month) userValues[parseInt(d.month) - 1] = Number(d.total) || 0; });
                    
                    new Chart(userCtx, {
                        type: 'line',
                        data: {
                            labels: monthsList,
                            datasets: [{
                                label: '👥 User Baru',
                                data: userValues,
                                borderColor: '#8faa7b',
                                backgroundColor: '#8faa7b20',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3,
                                pointBackgroundColor: '#8faa7b',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }
                        }
                    });
                }
            @endif

            // ========== 4. FILTER TOGGLE CUSTOM DATE ==========
            const filterType = document.getElementById('filter_type');
            const customDateRange = document.getElementById('customDateRange');
            
            if (filterType) {
                filterType.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customDateRange.classList.remove('hidden');
                    } else {
                        customDateRange.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endsection