@extends('layout.content')

@section('title', 'Trash - Product Management')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Trash - Produk Terhapus</h1>
            <p class="text-[#8b7a66] text-sm mt-1">Produk yang telah dihapus</p>
        </div>
        <a href="{{ route('contents.operator.productmanage') }}"
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
                <p class="text-gray-500 text-sm mb-1">Total Produk di Trash</p>
                <span class="bg-red-500 text-white text-sm font-semibold px-5 py-2 rounded-xl inline-block">
                    {{ $totalTrash ?? 0 }} Produk
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <h2 class="font-playfair font-semibold text-lg text-[#3a2c0a]">List Produk Terhapus</h2>
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
                                $imageUrl = isset($p->image) && $p->image ? asset('storage/' . $p->image) : asset('images/placeholder.png');
                            @endphp
                            <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors bg-red-50">
                                <td class="px-3 py-3"><img src="{{ $imageUrl }}" alt="{{ $productName }}"
                                        class="w-14 h-14 object-cover rounded-xl opacity-50"></td>
                                <td class="px-3 py-3 text-sm text-gray-400 line-through">{{ $productCode }}</td>
                                <td class="px-3 py-3 text-sm text-gray-400 line-through">{{ $productName }}</td>
                                <td class="px-3 py-3"><span
                                        class="bg-gray-200 text-gray-500 text-xs font-medium px-3 py-1 rounded-lg">{{ $categoryName }}</span>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-400">{{ $stockValue }}</td>
                                <td class="px-3 py-3 text-center">
                                    <form method="POST" action="{{ route('contents.operator.products.restore', $p->id) }}"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-[#c9973a] hover:bg-[#e8d5a8] text-white p-2 rounded-lg transition-colors"
                                            title="Restore">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500"><i
                                    class="fas fa-trash-alt text-4xl text-gray-300 mb-2 block"></i>
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
@endsection

@section('scripts')
    <style>
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const categoryFilter = document.getElementById('category-filter');
        function applySearchAndFilter() {
            const searchValue = searchInput?.value || '';
            const categoryValue = categoryFilter?.value || '';
            let url = "{{ route('contents.operator.productmanage.trash') }}?";
            const params = [];
            if (searchValue) params.push('search=' + encodeURIComponent(searchValue));
            if (categoryValue && categoryValue !== '') params.push('category_id=' + encodeURIComponent(categoryValue));
            url += params.join('&');
            window.location.href = url;
        }
        if (searchButton) searchButton.addEventListener('click', applySearchAndFilter);
        if (searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') applySearchAndFilter(); });
        if (categoryFilter) categoryFilter.addEventListener('change', applySearchAndFilter);
        setTimeout(function () { const toast = document.getElementById('toast-box'); if (toast) setTimeout(() => toast.style.display = 'none', 3000); }, 100);
    </script>
@endsection