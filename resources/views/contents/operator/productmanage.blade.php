@extends('layout.content')

@section('title', 'Product Management')

@section('content')
    <div class="bg-white p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
        <h2 class="text-2xl font-bold text-gray-800">Product Management</h2>
        <p class="text-[#8b7a66] mt-1">Kelola produk furniture Anda</p>
    </div>

    @if(session('success') || session('error'))
        <div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
            <div id="toast-box"
                class="px-8 py-4 rounded-2xl shadow-2xl text-white text-sm font-semibold
                                                                        {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-5 flex items-center gap-5 shadow">
            <div class="w-14 h-14 rounded-full bg-[#c9973a] flex items-center justify-center">
                <svg width="24" height="24" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Tambah Produk Baru</p>
                <button onclick="openModal('modal-tambah')"
                    class="bg-[#c9973a] hover:bg-[#b07e28] text-white text-sm font-semibold px-5 py-2 rounded-xl transition-all duration-200 hover:-translate-y-0.5">
                    Tambah Produk
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 flex items-center gap-5 shadow">
            <div class="w-14 h-14 rounded-full bg-[#c9973a] flex items-center justify-center">
                <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Produk</p>
                <span class="bg-[#c9973a] text-white text-sm font-semibold px-5 py-2 rounded-xl inline-block">
                    {{ isset($totalProduct) ? $totalProduct : 0 }} Produk
                </span>
            </div>
        </div>
    </div>

    {{-- TABLE PRODUCT --}}
    <div class="bg-white rounded-2xl p-6 shadow">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <h2 class="font-playfair font-semibold text-lg text-[#3a2c0a]">List Product</h2>
            <div class="flex gap-3 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a]" width="14" height="14" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" id="search-input" placeholder="Search Product..."
                        class="border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] transition-colors focus:border-[#c9973a] focus:outline-none w-64">
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
                    <option value="">All Category</option>
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
            <div class="mb-4 flex items-center gap-2">
                <span class="text-sm text-[#7a5c1e]">Menampilkan hasil untuk:</span>
                <span class="bg-[#c9973a] text-white text-xs px-3 py-1 rounded-full">
                    "{{ request('search') }}"
                </span>
                <a href="{{ route('contents.operator.productmanage') }}" class="text-xs text-[#c9973a] hover:underline">
                    Clear search
                </a>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#f3e4c3]">
                        <th class="rounded-l-xl px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">
                            Image</th>
                        <th class="px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">Kode</th>
                        <th class="px-3 py-3 text-left font-semibold text-[#7a5c1e] text-xs tracking-wider">Nama Item</th>
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
                                $categoryId = $p->category_id ?? 0;
                                $imageUrl = isset($p->image) && $p->image ? asset('storage/' . $p->image) : asset('images/placeholder.png');
                            @endphp
                            <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors">
                                <td class="px-3 py-3">
                                    <img src="{{ $imageUrl }}" alt="{{ $productName }}" class="w-14 h-14 object-cover rounded-xl">
                                </td>
                                <td class="px-3 py-3 text-sm text-[#3a3020]">{{ $productCode }}</td>
                                <td class="px-3 py-3 text-sm text-[#3a3020]">{{ $productName }}</td>
                                <td class="px-3 py-3">
                                    <span class="bg-[#f3e4c3] text-[#7a5c1e] text-xs font-medium px-3 py-1 rounded-lg">
                                        {{ $categoryName }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm text-[#3a3020]">{{ $stockValue }}</td>
                                <td class="px-3 py-3 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <button
                                            onclick="openEditModal({{ $productId }}, '{{ addslashes($productCode) }}', '{{ addslashes($productName) }}', {{ $categoryId }})"
                                            class="bg-[#c9973a] hover:bg-[#b07e28] text-white p-2 rounded-lg transition-colors">
                                            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            onclick="openDeleteModal({{ $p->id }}, '{{ addslashes($productName) }}')"
                                            class="bg-red-500 hover:bg-red-700 text-white p-2 rounded-lg transition-colors">
                                            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5"
                                                viewBox="0 0 24 24">
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
                            <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" />
                                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    </svg>
                                    <p>Tidak ada produk ditemukan</p>
                                    @if(request('search'))
                                        <a href="{{ route('contents.operator.productmanage') }}"
                                            class="text-[#c9973a] hover:underline text-sm">Clear search</a>
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
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH PRODUK --}}
    <div id="modal-tambah" class="modal-overlay">
        <div class="modal-animation bg-white rounded-3xl p-10 w-full max-w-lg mx-4 my-auto shadow-2xl">
            <div class="text-center mb-1">
                <span class="bg-[#c9973a] text-white text-xs font-bold tracking-wider px-5 py-1 rounded-full">LUXURY
                    FURNITURE</span>
            </div>
            <h2 class="font-playfair text-3xl font-bold text-center text-[#1a1208] mt-2 mb-7">Tambah Produk Baru</h2>
            <form method="POST" action="{{ route('contents.operator.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-[#3a3020]">Image</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3 cursor-pointer"
                            onclick="document.getElementById('add-image-input').click()">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            <input type="file" id="add-image-input" name="image" accept="image/*" class="hidden"
                                onchange="previewImage(this,'add-preview','add-file-label')">
                            <span id="add-file-label" class="text-[#b09060] text-sm truncate">Pilih gambar</span>
                        </div>
                        <div id="add-preview" class="hidden mt-1">
                            <img class="w-16 h-16 object-cover rounded-xl border border-[#e8d5a8]">
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-[#3a3020]">Kategori</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                            </svg>
                            <select name="category_id"
                                class="bg-transparent border-none outline-none text-sm text-[#3a3020] w-full cursor-pointer"
                                required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories ?? [] as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 col-span-2">
                        <label class="text-sm font-semibold text-[#3a3020]">Nama Produk</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                            </svg>
                            <input type="text" name="name" placeholder="Masukkan Nama Produk"
                                class="bg-transparent border-none outline-none text-sm text-[#3a3020] w-full placeholder:text-[#b09060]"
                                required>
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="mt-7 w-full bg-[#c9973a] hover:bg-[#b07e28] text-white font-bold text-base py-4 rounded-2xl transition-all duration-200 hover:-translate-y-0.5">Tambah</button>
            </form>
            <div class="text-center mt-4">
                <button onclick="closeModal('modal-tambah')"
                    class="text-[#9a8060] text-sm bg-transparent border-none cursor-pointer hover:text-[#7a5c1e] transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PRODUK --}}
    <div id="modal-edit" class="modal-overlay">
        <div class="modal-animation bg-white rounded-3xl p-10 w-full max-w-lg mx-4 my-auto shadow-2xl">
            <div class="text-center mb-1">
                <span class="bg-[#c9973a] text-white text-xs font-bold tracking-wider px-5 py-1 rounded-full">LUXURY
                    FURNITURE</span>
            </div>
            <h2 class="font-playfair text-3xl font-bold text-center text-[#1a1208] mt-2 mb-7">Edit Produk</h2>
            <form id="form-edit" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-[#3a3020]">Kode Produk</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <line x1="9" y1="9" x2="9" y2="15" />
                                <line x1="12" y1="9" x2="12" y2="15" />
                                <line x1="15" y1="9" x2="15" y2="15" />
                            </svg>
                            <input id="edit-code" type="text" name="product_code" placeholder="Edit Kode Produk"
                                class="bg-transparent border-none outline-none text-sm text-[#3a3020] w-full placeholder:text-[#b09060]"
                                required>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-[#3a3020]">Image</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3 cursor-pointer"
                            onclick="document.getElementById('edit-image-input').click()">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            <input type="file" id="edit-image-input" name="image" accept="image/*" class="hidden"
                                onchange="previewImage(this,'edit-preview','edit-file-label')">
                            <span id="edit-file-label" class="text-[#b09060] text-sm truncate">No file chosen</span>
                        </div>
                        <div id="edit-preview" class="hidden mt-1">
                            <img class="w-16 h-16 object-cover rounded-xl border border-[#e8d5a8]">
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-[#3a3020]">Category</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                            </svg>
                            <select id="edit-category" name="category_id"
                                class="bg-transparent border-none outline-none text-sm text-[#3a3020] w-full cursor-pointer"
                                required>
                                <option value="" disabled>Pilih Category</option>
                                @if(isset($categories) && count($categories) > 0)
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-[#3a3020]">Nama Produk</label>
                        <div class="flex items-center gap-2 bg-[#f3e4c3] rounded-xl px-4 py-3">
                            <svg width="18" height="18" fill="none" stroke="#c9973a" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1" />
                                <circle cx="20" cy="21" r="1" />
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                            </svg>
                            <input id="edit-name" type="text" name="name" placeholder="Edit Nama Produk"
                                class="bg-transparent border-none outline-none text-sm text-[#3a3020] w-full placeholder:text-[#b09060]"
                                required>
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="mt-7 w-full bg-[#c9973a] hover:bg-[#b07e28] text-white font-bold text-base py-4 rounded-2xl transition-all duration-200 hover:-translate-y-0.5">Update</button>
            </form>
            <div class="text-center mt-4">
                <button onclick="closeModal('modal-edit')"
                    class="text-[#9a8060] text-sm bg-transparent border-none cursor-pointer hover:text-[#7a5c1e] transition-colors">Tutup</button>
            </div>
        </div>
    </div>
    {{-- MODAL DELETE CONFIRMATION --}}
    <div id="modal-delete" class="modal-overlay">
        <div class="modal-animation bg-white rounded-3xl p-8 w-full max-w-md mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
                </div>
                <span class="bg-red-100 text-red-600 text-xs font-bold tracking-wider px-4 py-1 rounded-full">
                    PERINGATAN
                </span>
            </div>
            <h2 class="font-playfair text-2xl font-bold text-center text-[#1a1208] mb-3">Hapus Produk</h2>
            <p class="text-center text-gray-500 mb-6" id="delete-message">
                Yakin ingin menghapus produk <span id="delete-product-name" class="font-semibold text-gray-800"></span>?
            </p>

            <form id="form-delete" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('modal-delete')"
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition font-medium">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl transition font-medium">
                    <i class="fas fa-trash-alt mr-2"></i> Ya, Hapus
                </button>
            </form>
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
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Delete modal functions
        let deleteFormAction = '';

        function openDeleteModal(productId, productName) {
            deleteFormAction = '/contents/operator/products/' + productId;
            document.getElementById('delete-product-name').innerText = productName;
            openModal('modal-delete');
        }

        document.getElementById('form-delete')?.addEventListener('submit', function (e) {
            e.preventDefault();
            this.action = deleteFormAction;
            this.submit();
        });

        // Preview image function
        function previewImage(input, previewId, labelId) {
            const preview = document.getElementById(previewId);
            const label = document.getElementById(labelId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                    if (label) label.textContent = input.files[0].name;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Open edit modal with data
        function openEditModal(id, code, name, categoryId) {
            const form = document.getElementById('form-edit');
            form.action = "{{ route('contents.operator.products.update', ['id' => ':id']) }}".replace(':id', id);
            document.getElementById('edit-code').value = code;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-category').value = categoryId;
            document.getElementById('edit-preview').classList.add('hidden');
            document.getElementById('edit-file-label').textContent = 'No file chosen';
            openModal('modal-edit');
        }

        // Search functionality
        document.getElementById('search-button')?.addEventListener('click', function () {
            const search = document.getElementById('search-input').value;
            const category = document.getElementById('category-filter').value;
            let url = "{{ route('contents.operator.productmanage') }}";
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (category) params.push('category_id=' + category);
            if (params.length > 0) url += '?' + params.join('&');
            window.location.href = url;
        });

        document.getElementById('search-input')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') document.getElementById('search-button').click();
        });

        document.getElementById('category-filter')?.addEventListener('change', function () {
            const search = document.getElementById('search-input').value;
            const category = this.value;
            let url = "{{ route('contents.operator.productmanage') }}";
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (category) params.push('category_id=' + category);
            if (params.length > 0) url += '?' + params.join('&');
            window.location.href = url;
        });

        setTimeout(function () {
            const toast = document.getElementById('toast-box');
            if (toast) setTimeout(() => toast.style.visibility = 'hidden', 3000);
        }, 100);

        window.onclick = function (event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>
@endsection