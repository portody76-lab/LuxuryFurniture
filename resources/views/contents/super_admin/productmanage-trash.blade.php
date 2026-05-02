@extends('layout.content')

@section('title', 'Sampah')

@section('content')
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">

        @section('customHeader')
            <div class="bg-white p-5 sm:p-8 rounded-2xl mb-6 sm:mb-8 shadow-md border border-[#e7ddcf]">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-800">
                    Sampah
                </h2>
                <p class="text-[#8b7a66] text-sm sm:text-base mt-2">
                    Lihat produk yang telah di hapus
                </p>
            </div>
        @endsection

        <a href="{{ route('contents.productmanage') }}"
            class="bg-[#c9973a] hover:bg-[#a87922] text-white px-4 py-2 rounded-xl transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Produk
        </a>
    </div>

    @if(session('success') || session('error'))
        <div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
            <div id="toast-box" class="px-8 py-4 rounded-2xl shadow-2xl text-white text-sm font-semibold
                                        {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-5 flex items-center gap-5 shadow">
            <div class="w-14 h-14 rounded-full bg-red-500 flex items-center justify-center">
                <i class="fas fa-trash-alt text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Produk di Sampah</p>
                <span class="bg-red-500 text-white text-sm font-semibold px-5 py-2 rounded-xl inline-block">
                    {{ $totalTrash ?? 0 }} Produk
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <h2 class="font-playfair font-semibold text-lg text-[#3a2c0a]">Daftar Produk Terhapus</h2>
            <div class="flex gap-3 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a]" width="14" height="14" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" id="search-input" placeholder="Cari Produk..."
                        class="border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] transition-colors focus:border-[#c9973a] focus:outline-none w-48 sm:w-64">
                </div>
                <button id="search-button"
                    class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-4 py-2 rounded-xl transition-colors">
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
                <select id="category-filter"
                    class="border border-[#e8d5a8] rounded-xl py-2 px-4 text-sm bg-[#fdf8f0] cursor-pointer outline-none focus:border-[#c9973a]">
                    <option value="">Semua Kategori</option>
                    @if(isset($categories) && count($categories) > 0)
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#f3e4c3]">
                        <th class="rounded-l-xl px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">
                            Kode</th>
                        <th class="px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">Nama Produk</th>
                        <th class="px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">Kategori</th>
                        <th class="px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">Stok</th>
                        <th class="rounded-r-xl px-3 py-3 text-center font-semibold text-[#7a5c1e] text-xs tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($products) && count($products) > 0)
                        @foreach($products as $p)
                            @php
                                $productId = $p->id ?? 0;
                                $productCode = $p->product_code ?? '-';
                                $productName = $p->name ?? '-';
                                $categoryName = isset($p->category) && $p->category ? $p->category->name : 'Tidak Ada Kategori';
                                $stockValue = $p->stock ?? 0;
                            @endphp
                            <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors bg-red-50">
                                <td class="px-3 py-3 text-sm text-gray-400 line-through">{{ $productCode }}</td>
                                <td class="px-3 py-3 text-sm text-gray-400 line-through">{{ $productName }}</td>
                                <td class="px-3 py-3"><span
                                        class="bg-gray-200 text-gray-500 text-xs font-medium px-3 py-1 rounded-lg">{{ $categoryName }}</span>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-400">{{ $stockValue }}</td>
                                <td class="px-3 py-3 text-center">
                                    <button type="button"
                                        onclick="openRestoreModal({{ $productId }}, '{{ addslashes($productName) }}')"
                                        class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition-colors"
                                        title="Restore">
                                        <i class="fa-solid fa-arrows-rotate"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-trash-alt text-4xl text-gray-300 mb-2 block"></i>
                                <p>Tidak ada produk di trash</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(isset($products) && method_exists($products, 'links'))
            <div class="mt-6">{{ $products->links() }}</div>
        @endif
    </div>

    <!-- MODAL RESTORE -->
    <div id="modal-restore" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fa-solid fa-arrows-rotate text-green-500"></i> Kembalikan Produk
                </h3>
                <button onclick="closeModal('modal-restore')" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="restore-icon">
                    <i class="fa-solid fa-arrows-rotate text-green-500 text-5xl mb-3"></i>
                </div>
                <p class="text-gray-600 mb-2">
                    Yakin ingin mengembalikan produk <span id="restore-product-name"
                        class="font-semibold text-gray-800"></span>?
                </p>
                <p class="text-green-600 text-sm">
                    <i class="fas fa-info-circle"></i> Produk akan kembali ke daftar aktif
                </p>
            </div>
            <div class="modal-footer">
                <form id="form-restore" method="POST" class="flex gap-3 w-full">
                    @csrf
                    <button type="button" onclick="closeModal('modal-restore')" class="btn-cancel flex-1">
                        Batal
                    </button>
                    <button type="submit" class="btn-restore flex-1">
                        <span>
                            <i class="fa-solid fa-arrows-rotate text-white"></i> Ya, kembalikan
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
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

        .modal-container {
            background: white;
            border-radius: 1.5rem;
            width: 90%;
            max-width: 450px;
            margin: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalFadeIn 0.2s ease-out;
        }

        .modal-container-sm {
            background: white;
            border-radius: 1.5rem;
            width: 90%;
            max-width: 380px;
            margin: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalFadeIn 0.2s ease-out;
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

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            color: #9ca3af;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
            font-size: 1.25rem;
        }

        .modal-close:hover {
            color: #4b5563;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .restore-icon,
        .delete-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .btn-cancel {
            padding: 0.625rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            color: #6b7280;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .btn-cancel:hover {
            background: #f9fafb;
        }

        .btn-restore {
            padding: 0.625rem 1rem;
            background: #22c55e;
            border: none;
            border-radius: 0.75rem;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-restore:hover {
            background: #16a34a;
        }

        .btn-delete {
            padding: 0.625rem 1rem;
            background: #ef4444;
            border: none;
            border-radius: 0.75rem;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete:hover {
            background: #dc2626;
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

        @media (max-width: 640px) {
            .modal-header {
                padding: 1rem 1.25rem;
            }

            .modal-title {
                font-size: 1.125rem;
            }

            .modal-body {
                padding: 1.25rem;
            }

            .modal-footer {
                padding: 0.875rem 1.25rem;
            }

            .btn-cancel,
            .btn-restore,
            .btn-delete {
                padding: 0.5rem 0.875rem;
                font-size: 0.875rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        let restoreFormAction = '';
        function openRestoreModal(productId, productName) {
            restoreFormAction = '/contents/products/' + productId + '/restore';
            document.getElementById('restore-product-name').innerText = productName;
            openModal('modal-restore');
        }
        document.getElementById('form-restore')?.addEventListener('submit', function (e) {
            e.preventDefault();
            this.action = restoreFormAction;
            this.submit();
        });

        let forceDeleteFormAction = '';
        function openForceDeleteModal(productId, productName) {
            forceDeleteFormAction = '/contents/products/' + productId + '/force';
            document.getElementById('force-delete-product-name').innerText = productName;
            openModal('modal-force-delete');
        }
        document.getElementById('form-force-delete')?.addEventListener('submit', function (e) {
            e.preventDefault();
            this.action = forceDeleteFormAction;
            this.submit();
        });

        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const categoryFilter = document.getElementById('category-filter');

        function applySearchAndFilter() {
            const searchValue = searchInput?.value || '';
            const categoryValue = categoryFilter?.value || '';
            let url = "{{ route('contents.productmanage.trash') }}?";
            const params = [];
            if (searchValue) params.push('search=' + encodeURIComponent(searchValue));
            if (categoryValue && categoryValue !== '') params.push('category_id=' + encodeURIComponent(categoryValue));
            url += params.join('&');
            window.location.href = url;
        }

        if (searchButton) searchButton.addEventListener('click', applySearchAndFilter);
        if (searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') applySearchAndFilter(); });
        if (categoryFilter) categoryFilter.addEventListener('change', applySearchAndFilter);

        setTimeout(function () {
            const toast = document.getElementById('toast-box');
            if (toast) setTimeout(() => toast.style.display = 'none', 3000);
        }, 100);
    </script>
@endsection