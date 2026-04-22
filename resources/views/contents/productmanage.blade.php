@extends('layout.content')

@section('title', 'Product Management')

@section('content')
    {{-- TAMBAH overflow-x-hidden di container utama --}}
    <div class="overflow-x-hidden">
        <div class="bg-white p-4 sm:p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manajemen Produk</h2>
            <p class="text-[#8b7a66] text-sm sm:text-base mt-1">Kelola produk furniture Anda</p>
        </div>

        @if(session('success') || session('error'))
            <div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
                <div id="toast-box" class="px-8 py-4 rounded-2xl shadow-2xl text-white text-sm font-semibold
                                        {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
                    {{ session('success') ?? session('error') }}
                </div>
            </div>
        @endif

        {{-- Statistik Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <div class="bg-white rounded-2xl p-4 sm:p-5 flex items-center gap-4 sm:gap-5 shadow">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#c9973a] flex items-center justify-center shrink-0">
                    <svg width="20" height="20" sm:width="24" sm:height="24" fill="none" stroke="white" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Tambah Produk Baru</p>
                    <button onclick="openModal('modal-tambah')"
                        class="bg-[#c9973a] hover:bg-[#b07e28] text-white text-xs sm:text-sm font-semibold px-4 sm:px-5 py-1.5 sm:py-2 rounded-xl transition-all duration-200 hover:-translate-y-0.5">
                        Tambah Produk
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 flex items-center gap-4 sm:gap-5 shadow">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#c9973a] flex items-center justify-center shrink-0">
                    <svg width="20" height="20" sm:width="24" sm:height="24" fill="none" stroke="white" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Produk</p>
                    <span
                        class="bg-[#c9973a] text-white text-xs sm:text-sm font-semibold px-4 sm:px-5 py-1.5 sm:py-2 rounded-xl inline-block">
                        {{ isset($totalProduct) ? $totalProduct : 0 }} Produk
                    </span>
                </div>
            </div>
        </div>

        {{-- TABLE PRODUCT --}}
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 gap-3">
                <h2 class="font-playfair font-semibold text-base sm:text-lg text-[#3a2c0a]">Daftar Produk</h2>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-48">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a]" width="14" height="14"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="search-input" placeholder="Cari Produk..."
                            class="w-full border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] transition-colors focus:border-[#c9973a] focus:outline-none">
                    </div>
                    <button id="search-button"
                        class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-4 py-2 rounded-xl transition-colors">
                        <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                    <select id="category-filter"
                        class="border border-[#e8d5a8] rounded-xl py-2 px-3 text-sm bg-[#fdf8f0] cursor-pointer outline-none focus:border-[#c9973a]">
                        <option value="">Semua Kategori</option>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            @if(request('search'))
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-[#7a5c1e]">Menampilkan hasil untuk:</span>
                    <span class="bg-[#c9973a] text-white text-xs px-3 py-1 rounded-full break-all">
                        "{{ request('search') }}"
                    </span>
                    <a href="{{ route('contents.productmanage') }}" class="text-xs text-[#c9973a] hover:underline">
                        Reset
                    </a>
                </div>
            @endif

            <div style="overflow-x: auto; width: 100%;">
                <table style="min-width: 500px; width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr class="bg-[#f3e4c3]">
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #7a5c1e; font-size: 12px; border-top-left-radius: 12px;">Kode</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #7a5c1e; font-size: 12px;">Nama Item</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #7a5c1e; font-size: 12px;">Kategori</th>
                            <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: #7a5c1e; font-size: 12px;">Stok</th>
                            <th style="padding: 12px 8px; text-align: center; font-weight: 600; color: #7a5c1e; font-size: 12px; border-top-right-radius: 12px;">Aksi</th>
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
                                    $categoryId = $p->category_id ?? 0;
                                @endphp
                                <tr style="border-bottom: 1px solid #f3e4c3;">
                                    <td style="padding: 12px 8px; font-size: 13px; color: #3a3020;">{{ $productCode }}</td>
                                    <td style="padding: 12px 8px; font-size: 13px; color: #3a3020; word-break: break-word;">
                                        {{ $productName }}
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="background: #f3e4c3; color: #7a5c1e; font-size: 11px; padding: 4px 10px; border-radius: 8px; white-space: nowrap;">
                                            {{ $categoryName }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; font-size: 13px; color: #3a3020;">{{ $stockValue }}</td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center;">
                                            <button onclick="openEditModal({{ $productId }}, '{{ addslashes($productCode) }}', '{{ addslashes($productName) }}', {{ $categoryId }})"
                                                style="background: #c9973a; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer;">
                                                <svg width="12" height="12" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <button onclick="openDeleteModal({{ $p->id }}, '{{ addslashes($productName) }}')"
                                                style="background: #ef4444; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer;">
                                                <svg width="12" height="12" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M9 6V4h6v2" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="padding: 32px 8px; text-align: center; color: #9ca3af;">
                                    <div>
                                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24" style="margin: 0 auto 8px;">
                                            <circle cx="11" cy="11" r="8" />
                                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                        </svg>
                                        <p>Tidak ada produk ditemukan</p>
                                        @if(request('search'))
                                            <a href="{{ route('contents.productmanage') }}"
                                                style="color: #c9973a; font-size: 12px;">Clear search</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(isset($products) && method_exists($products, 'links'))
                <div style="margin-top: 24px; overflow-x: auto;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        {{-- MODAL TAMBAH PRODUK (TANPA GAMBAR) --}}
        <div id="modal-tambah" class="modal-overlay fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up" style="width: 90%; max-width: 360px;">
                <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-box text-[#c9973a] text-sm"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Tambah Produk Baru</h3>
                    </div>
                    <button onclick="closeModal('modal-tambah')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('contents.products.store') }}">
                    @csrf
                    <div class="px-3 py-3 space-y-2">
                        <!-- Kategori -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                            <select name="category_id"
                                class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm text-gray-700"
                                required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories ?? [] as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nama Produk -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                            <input type="text" name="name" placeholder="Masukkan Nama Produk"
                                class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm text-gray-700"
                                required>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                        <button type="button" onclick="closeModal('modal-tambah')"
                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                        <button type="submit"
                            class="px-3 py-1 bg-[#c9973a] hover:bg-[#b07e28] text-white rounded-lg transition shadow-sm text-xs">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT PRODUK (TANPA GAMBAR) --}}
        <div id="modal-edit" class="modal-overlay fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up" style="width: 90%; max-width: 360px;">
                <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-pen text-yellow-500 text-sm"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Edit Produk</h3>
                    </div>
                    <button onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <form id="form-edit" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-3 py-3 space-y-2">
                        <!-- Kode Produk (readonly) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Produk</label>
                            <input id="edit-code" type="text" name="product_code" placeholder="Kode Produk"
                                class="w-full px-2 py-1.5 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed"
                                readonly>
                        </div>

                        <!-- Nama Produk -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                            <input id="edit-name" type="text" name="name" placeholder="Edit Nama Produk"
                                class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm text-gray-700"
                                required>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                            <select id="edit-category" name="category_id"
                                class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm text-gray-700"
                                required>
                                <option value="" disabled>Pilih Kategori</option>
                                @if(isset($categories) && count($categories) > 0)
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                        <button type="button" onclick="closeModal('modal-edit')"
                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                        <button type="submit"
                            class="px-3 py-1 bg-[#c9973a] hover:bg-[#b07e28] text-white rounded-lg transition shadow-sm text-xs">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE CONFIRMATION --}}
        <div id="modal-delete" class="modal-overlay fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up" style="width: 90%; max-width: 360px;">
                <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-trash-alt text-red-500 text-sm"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Hapus Produk</h3>
                    </div>
                    <button onclick="closeModal('modal-delete')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="px-3 py-3 text-center">
                    <div class="w-10 h-10 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                    </div>
                    <p class="text-sm text-gray-600" id="delete-message">
                        Yakin ingin menghapus produk <span id="delete-product-name" class="font-semibold text-gray-800"></span>?
                    </p>
                    <p class="text-xs text-red-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>

                <form id="form-delete" method="POST" class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('modal-delete')"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg transition shadow-sm text-xs">
                        Ya, Hapus
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
            0% { opacity: 0; transform: translateY(-20px); }
            15% { opacity: 1; transform: translateY(0); }
            85% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); visibility: hidden; }
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f3e4c3;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c9973a;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #b07e28;
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

        let deleteFormAction = '';

        function openDeleteModal(productId, productName) {
            deleteFormAction = '/contents/products/' + productId;
            document.getElementById('delete-product-name').innerText = productName;
            openModal('modal-delete');
        }

        document.getElementById('form-delete')?.addEventListener('submit', function(e) {
            e.preventDefault();
            this.action = deleteFormAction;
            this.submit();
        });

        function openEditModal(id, code, name, categoryId) {
            const form = document.getElementById('form-edit');
            form.action = "{{ route('contents.products.update', ['id' => ':id']) }}".replace(':id', id);
            document.getElementById('edit-code').value = code;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-category').value = categoryId;
            openModal('modal-edit');
        }

        document.getElementById('search-button')?.addEventListener('click', function() {
            const search = document.getElementById('search-input').value;
            const category = document.getElementById('category-filter').value;
            let url = "{{ route('contents.productmanage') }}";
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (category) params.push('category_id=' + category);
            if (params.length > 0) url += '?' + params.join('&');
            window.location.href = url;
        });

        document.getElementById('search-input')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') document.getElementById('search-button').click();
        });

        document.getElementById('category-filter')?.addEventListener('change', function() {
            const search = document.getElementById('search-input').value;
            const category = this.value;
            let url = "{{ route('contents.productmanage') }}";
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (category) params.push('category_id=' + category);
            if (params.length > 0) url += '?' + params.join('&');
            window.location.href = url;
        });

        setTimeout(function() {
            const toast = document.getElementById('toast-box');
            if (toast) setTimeout(() => toast.style.visibility = 'hidden', 3000);
        }, 100);

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>
@endsection