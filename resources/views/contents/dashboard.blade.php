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
            [
                'title' => 'Total Produk',
                'value' => number_format($totalProducts),
                'icon' => 'fa-couch',
                'color' => '#b68b40',
                'route' => 'contents.productmanage',
            ],
            [
                'title' => 'Total Kategori',
                'value' => number_format($totalCategories),
                'icon' => 'fa-layer-group',
                'color' => '#8faa7b',
                'route' => 'contents.categories',
            ],
            [
                'title' => 'Total Stok',
                'value' => number_format($totalStock ?? 0),
                'icon' => 'fa-boxes',
                'color' => '#ab8e64',
                'route' => 'contents.stockmanage',
            ],
        ];

        if (in_array($role, ['admin', 'super_admin'])) {
            $cards[] = [
                'title' => 'Total Users',
                'value' => number_format($totalUsers),
                'icon' => 'fa-users',
                'color' => '#c9a87b',
                'route' => 'contents.users',
            ];
        }
    @endphp

    <div
        class="grid grid-cols-1 sm:grid-cols-2 {{ count($cards) == 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }} gap-4 sm:gap-6 mb-8 sm:mb-10">
        @foreach ($cards as $card)
            <a href="{{ route($card['route']) }}"
                class="block bg-white rounded-2xl shadow-md p-4 sm:p-6 flex items-center justify-between border-l-4 border-l-[{{ $card['color'] }}] hover:shadow-lg transition-all duration-200 hover:-translate-y-1 cursor-pointer">
                <div>
                    <p class="text-gray-400 text-xs sm:text-sm uppercase tracking-wider mb-1">{{ $card['title'] }}</p>
                    <p class="text-2xl sm:text-4xl font-bold text-gray-800">{{ $card['value'] }}</p>
                </div>
                <div
                    class="w-10 h-10 sm:w-14 sm:h-14 bg-[{{ $card['color'] }}]/10 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas {{ $card['icon'] }} text-lg sm:text-2xl" style="color: {{ $card['color'] }}"></i>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8 sm:mb-10">

        <!-- Chart Produk (Barang Masuk & Keluar) -->
        <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex justify-between items-center mb-4 sm:mb-5 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-bar text-[#b68b40] text-lg sm:text-xl"></i>
                    <h4 class="font-semibold text-gray-800 text-base sm:text-lg">Statistik Transaksi Produk</h4>
                </div>
                <!-- Filter untuk Chart Produk -->
                <div class="relative">
                    <button onclick="toggleProductFilter()" class="text-gray-400 hover:text-[#c9973a] transition">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                    <div id="productFilterPanel"
                        class="absolute right-0 top-8 mt-1 bg-white rounded-xl shadow-lg p-3 z-10 hidden"
                        style="min-width: 350px;">
                        <div class="space-y-2">
                            <select id="product_filter_type"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                                <option value="custom">📅 Custom</option>
                                <option value="daily">📅 Harian</option>
                                <option value="weekly">📆 Mingguan</option>
                                <option value="monthly">📅 Bulanan</option>
                            </select>
                            <div id="product_custom_range" class="flex gap-2">
                                <input type="date" id="product_start_date"
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Mulai">
                                <input type="date" id="product_end_date"
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Akhir">
                            </div>
                            <div class="flex gap-2">
                                <button onclick="applyProductFilter()"
                                    class="bg-[#c9973a] text-white px-3 py-1 rounded-lg text-sm">Filter</button>
                                <button onclick="resetProductFilter()"
                                    class="bg-gray-500 text-white px-3 py-1 rounded-lg text-sm">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                <canvas id="productChart"></canvas>
            </div>
        </div>

        <!-- Ranking Barang (Vertical Bar Chart) -->
        <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex justify-between items-center mb-4 sm:mb-5 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-simple text-[#c9973a] text-lg sm:text-xl"></i>
                    <h4 class="font-semibold text-gray-800 text-base sm:text-lg">🏆 Top 5 Barang Paling Aktif</h4>
                </div>
                <!-- Filter untuk Ranking Chart -->
                <div class="relative">
                    <button onclick="toggleRankingFilter()" class="text-gray-400 hover:text-[#c9973a] transition">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                    <div id="rankingFilterPanel"
                        class="absolute right-0 top-8 mt-1 bg-white rounded-xl shadow-lg p-3 z-10 hidden"
                        style="min-width: 350px;">
                        <div class="space-y-2">
                            <select id="ranking_filter_type"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                                <option value="custom">📅 Custom</option>
                                <option value="daily">📅 Harian</option>
                                <option value="weekly">📆 Mingguan</option>
                                <option value="monthly">📅 Bulanan</option>
                            </select>
                            <div id="ranking_custom_range" class="flex gap-2">
                                <input type="date" id="ranking_start_date"
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Mulai">
                                <input type="date" id="ranking_end_date"
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Akhir">
                            </div>
                            <div class="flex gap-2">
                                <button onclick="applyRankingFilter()"
                                    class="bg-[#c9973a] text-white px-3 py-1 rounded-lg text-sm">Filter</button>
                                <button onclick="resetRankingFilter()"
                                    class="bg-gray-500 text-white px-3 py-1 rounded-lg text-sm">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                <canvas id="rankingChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Users (Hanya untuk Admin & Super Admin) -->
    @if (in_array($role, ['admin', 'super_admin']))
        <div class="grid grid-cols-1 gap-4 sm:gap-6 mb-8 sm:mb-10">
            <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-center mb-4 sm:mb-5 pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-line text-[#8faa7b] text-lg sm:text-xl"></i>
                        <h4 class="font-semibold text-gray-800 text-base sm:text-lg">Statistik Pertumbuhan User</h4>
                    </div>
                    <!-- Filter untuk User Chart -->
                    <div class="relative">
                        <button onclick="toggleUserFilter()" class="text-gray-400 hover:text-[#c9973a] transition">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                        <div id="userFilterPanel"
                            class="absolute right-0 top-8 mt-1 bg-white rounded-xl shadow-lg p-3 z-10 hidden"
                            style="min-width: 350px;">
                            <div class="space-y-2">
                                <select id="user_filter_type"
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                                    <option value="custom">📅 Custom</option>
                                    <option value="daily">📅 Harian</option>
                                    <option value="weekly">📆 Mingguan</option>
                                    <option value="monthly">📅 Bulanan</option>
                                </select>
                                <div id="user_custom_range" class="flex gap-2">
                                    <input type="date" id="user_start_date"
                                        class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                        placeholder="Mulai">
                                    <input type="date" id="user_end_date"
                                        class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                        placeholder="Akhir">
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="applyUserFilter()"
                                        class="bg-[#c9973a] text-white px-3 py-1 rounded-lg text-sm">Filter</button>
                                    <button onclick="resetUserFilter()"
                                        class="bg-gray-500 text-white px-3 py-1 rounded-lg text-sm">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- BAGIAN BAWAH: Barang Stok Habis & Barang Rusak dengan Filter -->
    <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-[#c9973a]"></i> Manajemen Stok & Kerusakan
            </h3>

            <!-- Filter Temporal (Hanya untuk Barang Rusak) - AJAX -->
            <div class="flex flex-wrap items-center gap-2">
                <select id="damaged_filter_type"
                    class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:border-[#c9973a] focus:outline-none">
                    <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>📅 Custom</option>
                    <option value="daily" {{ $filterType == 'daily' ? 'selected' : '' }}>📅 Harian</option>
                    <option value="weekly" {{ $filterType == 'weekly' ? 'selected' : '' }}>📆 Mingguan</option>
                    <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>📅 Bulanan</option>
                </select>

                <div id="damaged_custom_range" class="flex gap-2 {{ $filterType == 'custom' ? '' : 'hidden' }}">
                    <input type="date" id="damaged_start_date" value="{{ $customStartDate }}"
                        class="border border-gray-300 rounded-xl px-3 py-2 text-sm" placeholder="Mulai">
                    <span class="text-gray-500">-</span>
                    <input type="date" id="damaged_end_date" value="{{ $customEndDate }}"
                        class="border border-gray-300 rounded-xl px-3 py-2 text-sm" placeholder="Akhir">
                </div>

                <button onclick="applyDamagedFilter()"
                    class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-filter"></i> Filter
                </button>

                <button onclick="resetDamagedFilter()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-sm transition">
                    <i class="fas fa-sync-alt"></i> Reset
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Barang Stok Akan Habis (Tanpa Filter) -->
            <div class="border border-orange-200 rounded-2xl overflow-hidden">
                <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                    <h4 class="font-semibold text-orange-700 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>Barang Stok Akan Habis
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
                        <tbody class="divide-y divide-gray-100" id="lowStockTableBody">
                            @forelse($lowStockProducts as $item)
                                <tr class="hover:bg-orange-50/50 transition">
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $item->product_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $item->category_name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                            {{ number_format($item->stock) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                                        {{ number_format($item->min_stock_threshold) }}
                                    </td>
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

            <!-- Barang Stok Rusak (Dengan Filter AJAX) -->
            <div class="border border-red-200 rounded-2xl overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h4 class="font-semibold text-red-700 flex items-center gap-2">
                        <i class="fas fa-times-circle"></i>Barang Stok Rusak
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
                        <tbody class="divide-y divide-gray-100" id="damagedStockTableBody">
                            @forelse($damagedStockProducts as $item)
                                <tr class="hover:bg-red-50/50 transition">
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $item->product_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            {{ number_format($item->damaged_quantity) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 max-w-37.5 truncate"
                                        title="{{ $item->description ?? '-' }}">
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
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, #f5e6c8 0%, #e8d5a8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state-icon i {
            font-size: 28px;
            color: #c9973a;
        }

        .empty-state-title {
            font-size: 16px;
            font-weight: 600;
            color: #5a4a1e;
            margin-bottom: 8px;
        }

        .empty-state-desc {
            font-size: 13px;
            color: #8b7a66;
            margin-bottom: 20px;
        }

        .overflow-y-auto::-webkit-scrollbar {
            width: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c9973a;
            border-radius: 10px;
        }

        .chart-container {
            position: relative;
            min-height: 350px;
        }

        .chart-empty-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
            z-index: 10;
            border-radius: 1rem;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Chart instances
        let productChart, rankingChart, userChart;

        // Initial data from server
        const initialMonths = @json($months);
        const initialInData = @json($inData);
        const initialOutData = @json($outData);
        const initialRankingLabels = @json(
            $rankingProducts->map(function ($item) {
                return strlen($item->name) > 15 ? substr($item->name, 0, 12) . '...' : $item->name;
            })
        );
        const initialRankingData = @json($rankingProducts->pluck('transaction_count'));

        @if (in_array($role, ['admin', 'super_admin']))
            const initialUserStats = @json($userStats ?? []);
            const monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const initialUserData = Array(12).fill(0);
            initialUserStats.forEach(d => {
                if (d.month) initialUserData[parseInt(d.month) - 1] = Number(d.total) || 0;
            });
        @endif

            // Initialize Product Chart
            function initProductChart(labels, inData, outData) {
                const canvas = document.getElementById('productChart');
                const ctx = canvas?.getContext('2d');
                if (!ctx) return;

                if (productChart) productChart.destroy();

                const hasData = inData.some(v => v > 0) || outData.some(v => v > 0);

                if (!hasData) {
                    canvas.style.display = 'none';
                    let emptyDiv = document.getElementById('productChartEmpty');
                    if (!emptyDiv) {
                        emptyDiv = document.createElement('div');
                        emptyDiv.id = 'productChartEmpty';
                        emptyDiv.className = 'empty-state absolute inset-0 flex items-center justify-center';
                        emptyDiv.innerHTML = `
                            <div class="text-center">
                                <div class="empty-state-icon mx-auto"><i class="fas fa-chart-line"></i></div>
                                <h3 class="empty-state-title">Belum Ada Data Transaksi</h3>
                                <p class="empty-state-desc">Belum ada transaksi yang tercatat untuk periode ini</p>
                            </div>
                        `;
                        canvas.parentNode.style.position = 'relative';
                        canvas.parentNode.appendChild(emptyDiv);
                    } else {
                        emptyDiv.style.display = 'flex';
                    }
                    return;
                }

                canvas.style.display = 'block';
                const emptyDiv = document.getElementById('productChartEmpty');
                if (emptyDiv) emptyDiv.style.display = 'none';

                productChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '📦 Barang Masuk',
                            data: inData,
                            backgroundColor: '#4caf50',
                            borderRadius: 8,
                            barPercentage: 0.65
                        },
                        {
                            label: '📤 Barang Keluar',
                            data: outData,
                            backgroundColor: '#f44336',
                            borderRadius: 8,
                            barPercentage: 0.65
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': ' + context.raw.toLocaleString() +
                                            ' unit';
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
                                    callback: function (value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    rotate: 45,
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            }

        // Initialize Ranking Chart
        function initRankingChart(labels, data) {
            const canvas = document.getElementById('rankingChart');
            const ctx = canvas?.getContext('2d');
            if (!ctx) return;

            if (rankingChart) rankingChart.destroy();

            const hasData = data.some(v => v > 0);

            if (!hasData || data.length === 0) {
                canvas.style.display = 'none';
                let emptyDiv = document.getElementById('rankingChartEmpty');
                if (!emptyDiv) {
                    emptyDiv = document.createElement('div');
                    emptyDiv.id = 'rankingChartEmpty';
                    emptyDiv.className = 'empty-state absolute inset-0 flex items-center justify-center';
                    emptyDiv.innerHTML = `
                            <div class="text-center">
                                <div class="empty-state-icon mx-auto"><i class="fas fa-star"></i></div>
                                <h3 class="empty-state-title">Belum Ada Data Transaksi</h3>
                                <p class="empty-state-desc">Belum ada transaksi yang tercatat untuk periode ini</p>
                            </div>
                        `;
                    canvas.parentNode.style.position = 'relative';
                    canvas.parentNode.appendChild(emptyDiv);
                } else {
                    emptyDiv.style.display = 'flex';
                }
                return;
            }

            canvas.style.display = 'block';
            const emptyDiv = document.getElementById('rankingChartEmpty');
            if (emptyDiv) emptyDiv.style.display = 'none';

            rankingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Frekuensi Transaksi',
                        data: data,
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
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
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
                                callback: function (value) {
                                    return value + 'x';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Transaksi',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                rotation: 0
                            },
                            title: {
                                display: true,
                                text: 'Nama Barang',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize User Chart
        function initUserChart(labels, data) {
            const canvas = document.getElementById('userChart');
            const ctx = canvas?.getContext('2d');
            if (!ctx) return;

            if (userChart) userChart.destroy();

            const hasData = data.some(v => v > 0);

            if (!hasData) {
                canvas.style.display = 'none';
                let emptyDiv = document.getElementById('userChartEmpty');
                if (!emptyDiv) {
                    emptyDiv = document.createElement('div');
                    emptyDiv.id = 'userChartEmpty';
                    emptyDiv.className = 'empty-state absolute inset-0 flex items-center justify-center';
                    emptyDiv.innerHTML = `
                            <div class="text-center">
                                <div class="empty-state-icon mx-auto"><i class="fas fa-users"></i></div>
                                <h3 class="empty-state-title">Belum Ada Data User</h3>
                                <p class="empty-state-desc">Belum ada user yang terdaftar untuk periode ini</p>
                            </div>
                        `;
                    canvas.parentNode.style.position = 'relative';
                    canvas.parentNode.appendChild(emptyDiv);
                } else {
                    emptyDiv.style.display = 'flex';
                }
                return;
            }

            canvas.style.display = 'block';
            const emptyDiv = document.getElementById('userChartEmpty');
            if (emptyDiv) emptyDiv.style.display = 'none';

            userChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '👥 User Baru',
                        data: data,
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
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Filter toggle functions
        function toggleProductFilter() {
            const panel = document.getElementById('productFilterPanel');
            panel.classList.toggle('hidden');
        }

        function toggleRankingFilter() {
            const panel = document.getElementById('rankingFilterPanel');
            panel.classList.toggle('hidden');
        }

        function toggleUserFilter() {
            const panel = document.getElementById('userFilterPanel');
            panel.classList.toggle('hidden');
        }

        // Close panels when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('#productFilterPanel') && !event.target.closest(
                'button[onclick="toggleProductFilter()"]')) {
                document.getElementById('productFilterPanel')?.classList.add('hidden');
            }
            if (!event.target.closest('#rankingFilterPanel') && !event.target.closest(
                'button[onclick="toggleRankingFilter()"]')) {
                document.getElementById('rankingFilterPanel')?.classList.add('hidden');
            }
            if (!event.target.closest('#userFilterPanel') && !event.target.closest(
                'button[onclick="toggleUserFilter()"]')) {
                document.getElementById('userFilterPanel')?.classList.add('hidden');
            }
        });

        // ========== AJAX FUNCTIONS FOR PRODUCT CHART ==========
        async function applyProductFilter() {
            const filterType = document.getElementById('product_filter_type').value;
            const startDate = document.getElementById('product_start_date').value;
            const endDate = document.getElementById('product_end_date').value;

            const params = new URLSearchParams({
                chart_type: 'product',
                filter_type: filterType,
                custom_start_date: startDate,
                custom_end_date: endDate
            });

            const url = '{{ route('contents.dashboard.chart-data') }}?' + params.toString();

            try {
                const response = await fetch(url);
                const result = await response.json();
                if (result.success) {
                    initProductChart(result.data.labels, result.data.inData, result.data.outData);
                }
                document.getElementById('productFilterPanel').classList.add('hidden');
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function resetProductFilter() {
            document.getElementById('product_filter_type').value = 'custom';
            document.getElementById('product_start_date').value = '';
            document.getElementById('product_end_date').value = '';
            applyProductFilter();
        }

        // ========== AJAX FUNCTIONS FOR RANKING CHART ==========
        async function applyRankingFilter() {
            const filterType = document.getElementById('ranking_filter_type').value;
            const startDate = document.getElementById('ranking_start_date').value;
            const endDate = document.getElementById('ranking_end_date').value;

            const params = new URLSearchParams({
                chart_type: 'ranking',
                filter_type: filterType,
                custom_start_date: startDate,
                custom_end_date: endDate
            });

            const url = '{{ route('contents.dashboard.chart-data') }}?' + params.toString();

            try {
                const response = await fetch(url);
                const result = await response.json();
                if (result.success) {
                    initRankingChart(result.data.labels, result.data.data);
                }
                document.getElementById('rankingFilterPanel').classList.add('hidden');
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function resetRankingFilter() {
            document.getElementById('ranking_filter_type').value = 'custom';
            document.getElementById('ranking_start_date').value = '';
            document.getElementById('ranking_end_date').value = '';
            applyRankingFilter();
        }

        // ========== AJAX FUNCTIONS FOR USER CHART ==========
        async function applyUserFilter() {
            const filterType = document.getElementById('user_filter_type').value;
            const startDate = document.getElementById('user_start_date').value;
            const endDate = document.getElementById('user_end_date').value;

            const params = new URLSearchParams({
                chart_type: 'user',
                filter_type: filterType,
                custom_start_date: startDate,
                custom_end_date: endDate
            });

            const url = '{{ route('contents.dashboard.chart-data') }}?' + params.toString();

            try {
                const response = await fetch(url);
                const result = await response.json();
                if (result.success) {
                    initUserChart(result.data.labels, result.data.data);
                }
                document.getElementById('userFilterPanel').classList.add('hidden');
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function resetUserFilter() {
            document.getElementById('user_filter_type').value = 'custom';
            document.getElementById('user_start_date').value = '';
            document.getElementById('user_end_date').value = '';
            applyUserFilter();
        }

        // ========== AJAX FUNCTIONS FOR DAMAGED STOCK ==========
        async function applyDamagedFilter() {
            const filterType = document.getElementById('damaged_filter_type').value;
            const startDate = document.getElementById('damaged_start_date').value;
            const endDate = document.getElementById('damaged_end_date').value;

            const params = new URLSearchParams({
                filter_type: filterType,
                custom_start_date: startDate,
                custom_end_date: endDate
            });

            const url = '{{ route('contents.dashboard.damaged-stock-data') }}?' + params.toString();

            try {
                const response = await fetch(url);
                const result = await response.json();
                if (result.success) {
                    updateDamagedStockTable(result.data);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function resetDamagedFilter() {
            // Reset dropdown ke custom
            document.getElementById('damaged_filter_type').value = 'custom';
            // Kosongkan input tanggal
            document.getElementById('damaged_start_date').value = '';
            document.getElementById('damaged_end_date').value = '';

            // TAMPILKAN kembali custom date range (karena sekarang nilai dropdown = custom)
            const damagedCustomRange = document.getElementById('damaged_custom_range');
            if (damagedCustomRange) {
                damagedCustomRange.classList.remove('hidden');
            }

            // Terapkan filter (akan menampilkan all-time)
            applyDamagedFilter();
        }

        function updateDamagedStockTable(data) {
            const tbody = document.getElementById('damagedStockTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-check-circle text-green-400 text-2xl mb-2 block"></i>
                        Tidak ada barang rusak
                    </td>
                </tr>
            `;
                return;
            }

            let html = '';
            data.forEach(item => {
                html += `
                <tr class="hover:bg-red-50/50 transition">
                    <td class="px-4 py-3 text-xs text-gray-600">${item.transaction_date}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">${item.product_code}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(item.name)}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                            ${item.damaged_quantity}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 max-w-37.5 truncate" title="${escapeHtml(item.description)}">
                        ${escapeHtml(item.description)}
                    </td>
                </tr>
            `;
            });
            tbody.innerHTML = html;
        }

        // Helper function to escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Initialize all charts and event listeners on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Product Chart
            initProductChart(initialMonths, initialInData, initialOutData);

            // Ranking Chart
            if (initialRankingLabels.length > 0) {
                initRankingChart(initialRankingLabels, initialRankingData);
            }

            // User Chart (Admin & Super Admin only)
            @if (in_array($role, ['admin', 'super_admin']))
                initUserChart(monthsList, initialUserData);
            @endif

                // ========== TOGGLE CUSTOM DATE RANGE FOR DAMAGED STOCK ==========
                const damagedFilterType = document.getElementById('damaged_filter_type');
            const damagedCustomRange = document.getElementById('damaged_custom_range');

            if (damagedFilterType && damagedCustomRange) {
                // Set initial state berdasarkan nilai awal dropdown
                if (damagedFilterType.value === 'custom') {
                    damagedCustomRange.classList.remove('hidden');
                } else {
                    damagedCustomRange.classList.add('hidden');
                }

                // Event listener untuk perubahan dropdown
                damagedFilterType.addEventListener('change', function () {
                    if (this.value === 'custom') {
                        damagedCustomRange.classList.remove('hidden');
                    } else {
                        damagedCustomRange.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endsection