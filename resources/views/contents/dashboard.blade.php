@extends('layout.content')

@section('title', auth()->user()->role->role_name === 'super_admin' ? 'Dashboard Super Admin' : (auth()->user()->role->role_name === 'admin' ? 'Dashboard Admin' : 'Dashboard Operator'))

@section('content')

    <div class="bg-white p-8 rounded-2xl mb-8 shadow-md border border-[#e7ddcf]">
        <h2 class="text-3xl font-bold text-gray-800">
            Halo, {{ auth()->user()->username }}
        </h2>
        <p class="text-[#8b7a66] text-base mt-2">
            Selamat datang di Dashboard {{ auth()->user()->role->role_name === 'super_admin' ? 'Super Admin' : (auth()->user()->role->role_name === 'admin' ? 'Admin' : 'Operator') }} Luxury Furniture
        </p>
    </div>

    <!-- Cards - 4 card untuk Super Admin dan Admin, 3 card untuk Operator -->
    @if(auth()->user()->role->role_name === 'super_admin' || auth()->user()->role->role_name === 'admin')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Card Total Produk -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#b68b40] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Produk</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#b68b40]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-couch text-2xl text-[#b68b40]"></i>
                </div>
            </div>

            <!-- Card Total Kategori -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#8faa7b] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Kategori</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalCategories) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#8faa7b]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-layer-group text-2xl text-[#8faa7b]"></i>
                </div>
            </div>

            <!-- Card Total Users (hanya untuk Super Admin dan Admin) -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#c9a87b] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Users</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalUsers ?? 0) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#c9a87b]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-[#c9a87b]"></i>
                </div>
            </div>

            <!-- Card Total Stok -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#ab8e64] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Stok</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalStock ?? 0) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#ab8e64]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-[#ab8e64]"></i>
                </div>
            </div>
        </div>
    @endif

    <!-- Untuk operator (3 card) -->
    @if(auth()->user()->role->role_name === 'operator')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Card Total Produk -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#b68b40] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Produk</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#b68b40]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-couch text-2xl text-[#b68b40]"></i>
                </div>
            </div>

            <!-- Card Total Kategori -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#8faa7b] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Kategori</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalCategories) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#8faa7b]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-layer-group text-2xl text-[#8faa7b]"></i>
                </div>
            </div>

            <!-- Card Total Stok -->
            <div
                class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between border-l-4 border-l-[#ab8e64] hover:shadow-lg transition-all duration-200">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider mb-1">Total Stok</p>
                    <p class="text-4xl font-bold text-gray-800">{{ number_format($totalStock ?? 0) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#ab8e64]/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-[#ab8e64]"></i>
                </div>
            </div>
        </div>
    @endif

    <!-- Charts -->
    <div
        class="grid grid-cols-1 {{ auth()->user()->role->role_name === 'operator' ? 'lg:grid-cols-2' : 'lg:grid-cols-3' }} gap-6">

        <!-- Chart Produk -->
        <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
                <i class="fas fa-chart-bar text-[#b68b40] text-xl"></i>
                <h4 class="font-semibold text-gray-800 text-lg">Statistik Transaksi Produk</h4>
            </div>

            @if(isset($productStats) && count($productStats) > 0 && collect($productStats)->sum('total') > 0)
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="productChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="empty-state-title">Belum Ada Data Transaksi</h3>
                    <p class="empty-state-desc">Belum ada transaksi yang tercatat</p>
                </div>
            @endif
        </div>

        <!-- Chart Users - HANYA untuk Super Admin dan Admin -->
        @if(auth()->user()->role->role_name === 'super_admin' || auth()->user()->role->role_name === 'admin')
            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
                    <i class="fas fa-chart-line text-[#8faa7b] text-xl"></i>
                    <h4 class="font-semibold text-gray-800 text-lg">Statistik Pertumbuhan User</h4>
                </div>

                @if(isset($userStats) && count($userStats) > 0 && collect($userStats)->sum('total') > 0)
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="userChart"></canvas>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="empty-state-title">Belum Ada Data User</h3>
                        <p class="empty-state-desc">Belum ada user yang terdaftar</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Chart Kategori -->
        <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
                <i class="fas fa-chart-pie text-[#c9a87b] text-xl"></i>
                <h4 class="font-semibold text-gray-800 text-lg">Distribusi Produk per Kategori</h4>
            </div>

            @if(isset($categoryStats) && count($categoryStats) > 0 && collect($categoryStats)->sum('total') > 0)
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="empty-state-title">Belum Ada Data Kategori</h3>
                    <p class="empty-state-desc">Belum ada produk yang dikategorikan</p>
                </div>
            @endif
        </div>

    </div>
@endsection

@section('scripts')
    {{-- script chart sama seperti sebelumnya --}}
    <style>
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #f5e6c8 0%, #e8d5a8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .empty-state-icon i {
            font-size: 32px;
            color: #c9973a;
        }
        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            color: #5a4a1e;
            margin-bottom: 8px;
        }
        .empty-state-desc {
            font-size: 14px;
            color: #8b7a66;
            margin-bottom: 20px;
        }
        .empty-state-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #c9973a;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .empty-state-btn:hover {
            background: #b07e28;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(201, 151, 58, 0.3);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productData = @json($productStats ?? []);
            const categoryData = @json($categoryStats ?? []);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            const barColor = '#b68b40';
            const lineColor = '#8faa7b';
            const categoryColors = ['#b68b40', '#c9a87b', '#8faa7b', '#ab8e64', '#d4b896', '#7a9e6e', '#e2c99a', '#6b8f5e', '#f0dfc0', '#5a7a50'];

            // Product Chart
            const productCtx = document.getElementById('productChart')?.getContext('2d');
            if (productCtx && productData.length > 0) {
                new Chart(productCtx, {
                    type: 'bar',
                    data: {
                        labels: productData.map(d => months[parseInt(d.month) - 1] || '-'),
                        datasets: [{
                            label: 'Jumlah Terjual',
                            data: productData.map(d => Number(d.total) || 0),
                            backgroundColor: barColor,
                            borderRadius: 8,
                            barPercentage: 0.65,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f0e6d3' }, ticks: { stepSize: 1, precision: 0, font: { size: 12 } } },
                            x: { grid: { display: false }, ticks: { font: { size: 12 } } }
                        }
                    }
                });
            }

            // User Chart (Super Admin & Admin only)
            @if(auth()->user()->role->role_name === 'super_admin' || auth()->user()->role->role_name === 'admin')
                const userData = @json($userStats ?? []);
                const userCtx = document.getElementById('userChart')?.getContext('2d');
                if (userCtx && userData.length > 0) {
                    new Chart(userCtx, {
                        type: 'line',
                        data: {
                            labels: userData.map(d => months[parseInt(d.month) - 1] || '-'),
                            datasets: [{
                                label: 'User Baru',
                                data: userData.map(d => Number(d.total) || 0),
                                borderColor: lineColor,
                                backgroundColor: lineColor + '20',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3,
                                pointBackgroundColor: lineColor,
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
                                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, font: { size: 12 } } }
                            }
                        }
                    });
                }
            @endif

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
            if (categoryCtx && categoryData.length > 0) {
                new Chart(categoryCtx, {
                    type: 'pie',
                    data: {
                        labels: categoryData.map(d => d.name),
                        datasets: [{
                            data: categoryData.map(d => Number(d.total) || 0),
                            backgroundColor: categoryColors.slice(0, categoryData.length),
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { size: 12 }, usePointStyle: true, boxWidth: 12 } }
                        }
                    }
                });
            }
        });
    </script>
@endsection