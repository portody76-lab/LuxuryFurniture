{{--  --}}

@extends('layout.content')

@section('title', 'Dashboard')

@section('content')
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
                <p class="text-gray-500 text-sm">Total Stok</p>
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
    {{--  --}}

@endsection

@section('scripts')
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
@endsection