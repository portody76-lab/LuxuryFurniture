@extends('layout.content')

@section('title', 'Mutasi Stok - Luxury Furniture')

@section('content')
    <div class="bg-white p-5 sm:p-8 rounded-2xl mb-6 sm:mb-8 shadow-md border border-[#e7ddcf]">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800">Mutasi Stok</h2>
        <p class="text-[#8b7a66] text-sm sm:text-base mt-2">
            Riwayat mutasi stok barang secara lengkap
        </p>
    </div>

    <!-- Filter Section - Desain Baru -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
        <!-- Header Filter dengan warna aksen -->
        <div class="bg-gradient-to-r from-[#f5e6c8] to-[#e8d5a8] px-4 sm:px-6 py-3 sm:py-4 border-b border-[#d4c4a0]">
            <h3 class="font-semibold text-[#5a4a1e] flex items-center gap-2">
                <i class="fas fa-sliders-h text-[#c9973a]"></i> Filter Data Mutasi
            </h3>
        </div>

        <form method="GET" action="{{ route('contents.mutasi') }}" class="p-4 sm:p-6">
            <!-- Baris 1: Produk & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="relative">
                    <label class="block text-xs font-semibold text-[#8b7a66] uppercase tracking-wider mb-1">Produk</label>
                    <div class="relative">
                        <i class="fas fa-box absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                        <select name="product_id"
                            class="w-full pl-9 pr-3 py-2.5 border border-[#e8d5a8] rounded-xl bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none text-sm text-gray-700 appearance-none cursor-pointer">
                            <option value="">-- Semua Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ $selectedProductId == $product->id ? 'selected' : '' }}>
                                    {{ $product->product_code }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <i
                            class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-xs font-semibold text-[#8b7a66] uppercase tracking-wider mb-1">Kategori</label>
                    <div class="relative">
                        <i class="fas fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                        <select name="category_id"
                            class="w-full pl-9 pr-3 py-2.5 border border-[#e8d5a8] rounded-xl bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none text-sm text-gray-700 appearance-none cursor-pointer">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <i
                            class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Baris 2: Periode & Custom Date -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-semibold text-[#8b7a66] uppercase tracking-wider mb-1">Periode</label>
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                        <select name="filter_type" id="filter_type"
                            class="w-full pl-9 pr-3 py-2.5 border border-[#e8d5a8] rounded-xl bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none text-sm text-gray-700 appearance-none cursor-pointer">
                            <option value="daily" {{ $selectedFilterType == 'daily' ? 'selected' : '' }}>📅 Harian</option>
                            <option value="weekly" {{ $selectedFilterType == 'weekly' ? 'selected' : '' }}>📆 Mingguan
                            </option>
                            <option value="monthly" {{ $selectedFilterType == 'monthly' ? 'selected' : '' }}>📅 Bulanan
                            </option>
                        </select>
                        <i
                            class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-[#8b7a66] uppercase tracking-wider mb-1">Custom
                        Tanggal</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <i
                                class="fas fa-calendar-day absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                            <input type="date" name="custom_start_date" value="{{ $selectedCustomStartDate }}"
                                class="w-full pl-9 pr-3 py-2.5 border border-[#e8d5a8] rounded-xl bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none text-sm text-gray-700">
                        </div>
                        <div class="hidden sm:flex items-center text-[#c9973a]">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="relative flex-1">
                            <i
                                class="fas fa-calendar-week absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                            <input type="date" name="custom_end_date" value="{{ $selectedCustomEndDate }}"
                                class="w-full pl-9 pr-3 py-2.5 border border-[#e8d5a8] rounded-xl bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none text-sm text-gray-700">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                        <i class="fas fa-info-circle text-[10px]"></i> Kosongkan untuk tidak menggunakan filter custom
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-[#f0e6d3]">
                <button type="submit"
                    class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-6 py-2.5 rounded-xl transition-all duration-200 text-sm font-semibold flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                    <i class="fas fa-search text-sm"></i> Terapkan Filter
                </button>
                <a href="{{ route('contents.mutasi') }}"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-2.5 rounded-xl transition-all duration-200 text-sm font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt text-sm"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#f3e4c3]">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Kode Produk</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Nama Produk</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Kategori</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-[#7a5c1e]">Jenis</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-[#7a5c1e]">Jumlah</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-[#7a5c1e]">Kondisi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $item)
                        <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors">
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $transactions->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">
                                {{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $item->product_code }}</td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $item->category_name }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $item->type == 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-[#3a3020]">{{ number_format($item->quantity) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $item->condition == 'good' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $item->condition == 'good' ? 'Aman' : 'Rusak' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-[#3a3020] max-w-xs truncate"
                                title="{{ $item->description ?? '-' }}">
                                {{ $item->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $item->user_name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                                <i class="fas fa-database text-4xl mb-3 block"></i>
                                <p>Tidak ada data transaksi</p>
                                <p class="text-sm mt-1">Belum ada mutasi stok yang tercatat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-4 border-t border-gray-100">
            {{ $transactions->withQueryString()->links() }}
        </div>

        <div class="px-4 py-3 bg-gray-50 text-right text-xs text-gray-500 border-t border-gray-100">
            Total data: {{ $transactions->total() }} record
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Styling scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c9973a;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #b07e28;
        }

        /* Tooltip styling */
        [title] {
            cursor: help;
        }
    </style>

    <script>
        // Toggle custom date range
        const filterType = document.getElementById('filter_type');
        const customDateRange = document.getElementById('customDateRange');

        if (filterType) {
            filterType.addEventListener('change', function () {
                if (this.value === 'custom') {
                    customDateRange.classList.remove('hidden');
                } else {
                    customDateRange.classList.add('hidden');
                }
            });
        }
    </script>
@endsection