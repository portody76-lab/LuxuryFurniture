<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - Operator Luxury Furniture</title>

    @vite(['resources/css/app.css'])

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-animation {
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        #toast-box {
            animation: toastFade 3s ease-in-out forwards;
        }

        @keyframes toastFade {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            15% {
                opacity: 1;
                transform: translateY(0);
            }

            85% {
                opacity: 1;
                transform: translateY(0);
            }

            100% {
                opacity: 0;
                transform: translateY(-20px);
                visibility: hidden;
            }
        }

        .stock-low {
            background-color: #fee2e2;
            color: #dc2626;
            font-weight: bold;
        }

        .stock-normal {
            background-color: #dcfce7;
            color: #16a34a;
            font-weight: bold;
        }

        .btn-action {
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="min-h-screen" style="background: linear-gradient(135deg, #f5e6c8 0%, #e8d5a8 50%, #dfc99a 100%);">

    {{-- Toast Notification --}}
    @if(session('success') || session('error'))
        <div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
            <div id="toast-box" class="px-8 py-4 rounded-2xl shadow-2xl text-white text-sm font-semibold
                            {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        @include('layout.sidebar.sidebar_operator')

        {{-- MAIN CONTENT --}}
        <main class="flex-1 p-8">

            {{-- Header --}}
            <div class="bg-white p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
                <h2 class="text-2xl font-bold text-gray-800">Stock Management</h2>
                <p class="text-[#8b7a66] mt-1">Kelola stok produk furniture Anda</p>
            </div>

            {{-- Total Stok Card --}}
            <div class="bg-white rounded-2xl p-5 flex items-center gap-5 shadow mb-6">
                <div class="w-14 h-14 rounded-full bg-[#c9973a] flex items-center justify-center">
                    <i class="fas fa-boxes text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Stok Keseluruhan</p>
                    <span class="bg-[#c9973a] text-white text-2xl font-bold px-5 py-2 rounded-xl inline-block">
                        {{ number_format($totalStock ?? 0) }} unit
                    </span>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white rounded-2xl p-6 shadow">
                <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                    <h2 class="font-playfair font-semibold text-lg text-[#3a2c0a]">Daftar Stok Produk</h2>
                    <div class="flex gap-3 flex-wrap">
                        <div class="relative">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                            <input type="text" id="search-input" placeholder="Cari produk..."
                                class="border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none w-64">
                        </div>
                        <select id="category-filter"
                            class="border border-[#e8d5a8] rounded-xl py-2 px-4 text-sm bg-[#fdf8f0] cursor-pointer focus:border-[#c9973a]">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Search Active Indicator --}}
                @if(request('search'))
                    <div class="mb-4 flex items-center gap-2">
                        <span class="text-sm text-[#7a5c1e]">Menampilkan hasil untuk:</span>
                        <span class="bg-[#c9973a] text-white text-xs px-3 py-1 rounded-full">
                            "{{ request('search') }}"
                        </span>
                        <a href="{{ route('contents.operator.stock') }}" class="text-xs text-[#c9973a] hover:underline">
                            Clear search
                        </a>
                    </div>
                @endif

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#f3e4c3]">
                                <th class="rounded-l-xl px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">No
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">Nama Produk</th>
                                <th class="px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">Kategori</th>
                                <th class="px-4 py-3 text-center font-semibold text-[#7a5c1e] text-sm">Stok</th>
                                <th class="rounded-r-xl px-4 py-3 text-center font-semibold text-[#7a5c1e] text-sm">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $p)
                                @php
                                    $stockClass = ($p->stock ?? 0) <= 5 ? 'stock-low' : 'stock-normal';
                                    $stockClass = ($p->stock ?? 0) == 0 ? 'stock-low' : $stockClass;
                                @endphp
                                <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors">
                                    <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $p->product_code }}</td>
                                    <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $p->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-[#f3e4c3] text-[#7a5c1e] text-xs font-medium px-3 py-1 rounded-lg">
                                            {{ $p->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-semibold px-3 py-1 rounded-lg {{ $stockClass }}">
                                            {{ number_format($p->stock ?? 0) }}
                                        </span>
                                        @if(($p->stock ?? 0) <= 5 && ($p->stock ?? 0) > 0)
                                            <span class="text-xs text-red-500 block">⚠️ Stok menipis!</span>
                                        @elseif(($p->stock ?? 0) == 0)
                                            <span class="text-xs text-red-500 block">❌ Habis!</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <button
                                                onclick="openStockModal('add', {{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->stock ?? 0 }})"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg transition-all btn-action text-sm">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                            <button
                                                onclick="openStockModal('remove', {{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->stock ?? 0 }})"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-all btn-action text-sm">
                                                <i class="fas fa-minus"></i> Kurang
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                        <i class="fas fa-box-open text-3xl mb-2 block"></i>
                                        Tidak ada produk ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL TAMBAH/KURANG STOK --}}
    <div id="stockModal" class="modal-overlay">
        <div class="modal-animation bg-white rounded-3xl p-8 w-full max-w-md mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <span class="bg-[#c9973a] text-white text-xs font-bold tracking-wider px-5 py-1 rounded-full">
                    STOCK MANAGEMENT
                </span>
            </div>
            <h2 id="modalTitle" class="font-playfair text-2xl font-bold text-center text-[#1a1208] mt-2 mb-6">
                Tambah Stok
            </h2>

            <form id="stockForm" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="product_id">

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Produk</label>
                    <div class="bg-[#f3e4c3] rounded-xl px-4 py-3">
                        <span id="product_name" class="text-sm text-[#3a3020]"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Stok Saat Ini</label>
                    <div class="bg-gray-100 rounded-xl px-4 py-3">
                        <span id="current_stock_display" class="text-sm text-gray-600">0</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Jumlah</label>
                    <input type="number" name="quantity" id="quantity" required min="1"
                        class="w-full px-4 py-3 border border-[#e8d5a8] rounded-xl focus:outline-none focus:border-[#c9973a]">
                </div>



                <button type="submit" id="submitBtn"
                    class="w-full bg-[#c9973a] hover:bg-[#b07e28] text-white font-bold py-3 rounded-xl transition-all duration-200 hover:-translate-y-0.5">
                    Proses
                </button>
            </form>

            <div class="text-center mt-4">
                <button onclick="closeModal('stockModal')"
                    class="text-[#9a8060] text-sm hover:text-[#7a5c1e] transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openStockModal(type, productId, productName, currentStock = 0) {
            const form = document.getElementById('stockForm');
            const title = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitBtn');
            const quantityInput = document.getElementById('quantity');

            document.getElementById('product_id').value = productId;
            document.getElementById('product_name').innerText = productName;
            document.getElementById('current_stock_display').innerText = currentStock;
            document.getElementById('quantity').value = '';
            // HAPUS BARIS INI: document.getElementById('note').value = '';

            if (type === 'add') {
                title.innerText = 'Tambah Stok';
                submitBtn.innerHTML = '<i class="fas fa-plus mr-2"></i> Tambah Stok';
                form.action = "{{ route('contents.operator.stock.add') }}";
                quantityInput.max = '';
                quantityInput.placeholder = 'Masukkan jumlah stok masuk';
            } else {
                title.innerText = 'Kurang Stok';
                submitBtn.innerHTML = '<i class="fas fa-minus mr-2"></i> Kurang Stok';
                form.action = "{{ route('contents.operator.stock.remove') }}";
                quantityInput.max = currentStock;
                quantityInput.placeholder = 'Maksimal ' + currentStock;

                if (currentStock <= 0) {
                    alert('Stok habis! Tidak bisa mengurangi stok.');
                    return;
                }
            }

            openModal('stockModal');
        }

        // Search functionality
        function applyFilter() {
            const search = document.getElementById('search-input').value;
            const category = document.getElementById('category-filter').value;
            let url = "{{ route('contents.operator.stock') }}?";
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (category) params.push('category_id=' + category);
            if (params.length > 0) {
                window.location.href = url + params.join('&');
            } else {
                window.location.href = url.slice(0, -1);
            }
        }

        // Event listeners
        document.getElementById('search-input')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') applyFilter();
        });

        document.getElementById('category-filter')?.addEventListener('change', applyFilter);

        // Set value from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam && document.getElementById('search-input')) {
            document.getElementById('search-input').value = searchParam;
        }

        // Auto hide toast
        setTimeout(() => {
            const toast = document.getElementById('toast-box');
            if (toast) {
                setTimeout(() => toast.style.visibility = 'hidden', 3000);
            }
        }, 100);

        // Close modal on outside click
        window.onclick = function (event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>

</body>

</html>