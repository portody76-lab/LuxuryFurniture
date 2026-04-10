<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Luxury Furniture</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-[#f4efe3] font-sans">

    <div class="flex min-h-screen">

        <!-- Sidebar - DESIGN SESUAI GAMBAR (warna kayu/coklat mewah) -->
        <div class="w-64 bg-[#cbb892] p-6 shadow-lg flex flex-col justify-between">

            <!-- TOP (LOGO + MENU) -->
            <div>

                <!-- Logo -->
                <div class="flex items-center gap-2 mb-8 border-b border-[#a88454] pb-4">
                    <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-72">
                </div>

                <!-- Menu -->
                <div class="space-y-2">

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('admin.dashboard') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                        <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                    </a>

                    <a href="{{ route('admin.manage-admin') }}"
                        class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('admin.manage-admin') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                        <i class="fas fa-user-cog w-5"></i> Manage Account Admin
                    </a>

                    <a href="{{ route('admin.users') }}"
                        class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('admin.users') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                        <i class="fas fa-users w-5"></i> User Management
                    </a>

                    <a href="{{ route('admin.categories') }}"
                        class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('admin.categories') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                        <i class="fas fa-tags w-5"></i> Category
                    </a>

                    <a href="{{ route('admin.reports') }}"
                        class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('admin.reports') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                        <i class="fas fa-chart-line w-5"></i> Report
                    </a>

                </div>
            </div>

            <!-- BOTTOM (LOGOUT) -->
            <form action="/logout" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 p-3 rounded-lg text-red-700 hover:bg-red-100 transition">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    Logout
                </button>
            </form>

        </div>  

        <!-- Main -->
        <div class="flex-1 p-6">

            <!-- Header - DESIGN lebih elegan -->
            <div class="bg-white p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
                <h2 class="text-2xl font-bold text-gray-800">Halo, Admin</h2>
                <p class="text-[#8b7a66] mt-1">Selamat datang di Dashboard Admin Luxury Furniture</p>
            </div>

            <!-- Cards - DESIGN dengan icon dan border accent -->
            <div class="grid grid-cols-4 gap-4 mb-6">

                <div class="bg-white p-5 rounded-xl shadow-md flex items-center justify-between border-l-4 border-l-[#b68b40]">
                    <div>
                        <p class="text-gray-500 text-sm">Total Produk</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</h2>
                    </div>
                    <i class="fas fa-couch text-2xl text-[#b68b40]"></i>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-md flex items-center justify-between border-l-4 border-l-[#8faa7b]">
                    <div>
                        <p class="text-gray-500 text-sm">Total Kategori</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $totalCategories }}</h2>
                    </div>
                    <i class="fas fa-layer-group text-2xl text-[#8faa7b]"></i>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-md flex items-center justify-between border-l-4 border-l-[#c9a87b]">
                    <div>
                        <p class="text-gray-500 text-sm">Total Users</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h2>
                    </div>
                    <i class="fas fa-users text-2xl text-[#c9a87b]"></i>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-md flex items-center justify-between border-l-4 border-l-[#ab8e64]">
                    <div>
                        <p class="text-gray-500 text-sm">Total Barang</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $totalStock ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-boxes text-2xl text-[#ab8e64]"></i>
                </div>

            </div>

            <!-- Charts - DESIGN dengan card yang lebih rapi -->
            <div class="grid grid-cols-3 gap-4">

                <div class="bg-white p-4 rounded-xl shadow-md">
                    <h4 class="mb-3 font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-[#b68b40]"></i> Statistik Produk
                    </h4>
                    <canvas id="productChart"></canvas>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-md">
                    <h4 class="mb-3 font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-chart-line text-[#8faa7b]"></i> Statistik Users
                    </h4>
                    <canvas id="userChart"></canvas>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-md">
                    <h4 class="mb-3 font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-[#c9a87b]"></i> Distribusi Stok
                    </h4>
                    <canvas id="categoryChart"></canvas>
                </div>

            </div>
        </div>
    </div>

    <script>
        // ========== LOGIKA ASLI, TIDAK DIUBAH SATUPUN ==========
        const productData = @json($productStats ?? []);
        const userData = @json($userStats ?? []);
        const categoryData = @json($categoryStats ?? []);

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // PRODUCT CHART
        new Chart(document.getElementById('productChart'), {
            type: 'bar',
            data: {
                labels: productData.map(d => {
                    const m = parseInt(d.month);
                    return months[m - 1] ?? '-';
                }),
                datasets: [{
                    data: productData.map(d => d.total ?? 0)
                }]
            }
        });

        // USER CHART (FIXED)
        new Chart(document.getElementById('userChart'), {
            type: 'line',
            data: {
                labels: userData.map(d => {
                    const m = parseInt(d.month);
                    return months[m - 1] ?? '-';
                }),
                datasets: [{
                    data: userData.map(d => d.total ?? 0)
                }]
            },
            options: {
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

        // CATEGORY CHART
        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: categoryData.map(d => d.name ?? '-'),
                datasets: [{
                    data: categoryData.map(d => d.total ?? 0)
                }]
            }
        });
    </script>

</body>

</html>