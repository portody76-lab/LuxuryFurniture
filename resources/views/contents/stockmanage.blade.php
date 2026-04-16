@extends('layout.content')

@section('title', 'Stock Management')

@section('content')
    <div class="bg-white p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
        <h2 class="text-2xl font-bold text-gray-800">Stock Management</h2>
        <p class="text-[#8b7a66] mt-1">Kelola stok produk furniture Anda</p>
    </div>

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

    <div class="bg-white rounded-2xl p-6 shadow">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <h2 class="font-playfair font-semibold text-lg text-[#3a2c0a]">Daftar Stok Produk</h2>
            <div class="flex gap-3 flex-wrap">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a] text-sm"></i>
                    <input type="text" id="search-input" placeholder="Cari produk..."
                        class="border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] focus:border-[#c9973a] focus:outline-none w-64">
                </div>
                <select id="category-filter"
                    class="border border-[#e8d5a8] rounded-xl py-2 px-4 text-sm bg-[#fdf8f0] cursor-pointer focus:border-[#c9973a]">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (request('search'))
            <div class="mb-4 flex items-center gap-2">
                <span class="text-sm text-[#7a5c1e]">Menampilkan hasil untuk:</span>
                <span class="bg-[#c9973a] text-white text-xs px-3 py-1 rounded-full">"{{ request('search') }}"</span>
                <a href="{{ route('stock') }}" class="text-xs text-[#c9973a] hover:underline">Clear search</a>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#f3e4c3]">
                        <th class="rounded-l-xl px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">Nama Produk</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#7a5c1e] text-sm">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold text-[#7a5c1e] text-sm">Stok</th>
                        <th class="rounded-r-xl px-4 py-3 text-center font-semibold text-[#7a5c1e] text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $p)
                        @php
                            if ($p->stock <= 0) {
                                $stockClass = 'stock-zero';
                            } elseif ($p->stock <= ($p->min_stock_threshold ?? 25)) {
                                $stockClass = 'stock-warning';
                            } else {
                                $stockClass = 'stock-normal';
                            }
                        @endphp
                        <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors">
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $p->product_code }}</td>
                            <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $p->name }}</td>
                            <td class="px-4 py-3"><span class="bg-[#f3e4c3] text-[#7a5c1e] text-xs font-medium px-3 py-1 rounded-lg">{{ $p->category->name ?? '-' }}</span></td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-semibold px-3 py-1 rounded-lg {{ $stockClass }}">{{ number_format($p->stock ?? 0) }}</span>
                                @if (($p->stock ?? 0) <= 25 && ($p->stock ?? 0) > 0)
                                    <span class="text-xs text-orange-500 block">⚠️ Stok akan habis!</span>
                                @elseif(($p->stock ?? 0) == 0)
                                    <span class="text-xs text-red-500 block">❌ Habis!</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2 flex-wrap">
                                    <button onclick="openAddStockModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->stock ?? 0 }})"
                                        class="bg-green-500 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                    <button onclick="openRemoveStockModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->stock ?? 0 }})"
                                        class="bg-red-500 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-minus"></i> Kurang
                                    </button>
                                    <button onclick="showHistory({{ $p->id }})"
                                        class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-history"></i> Histori
                                    </button>
                                    <button onclick="showDetail({{ $p->id }})"
                                        class="bg-gray-500 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-info-circle"></i> Detail
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-box-open text-3xl mb-2 block"></i>Tidak ada produk ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    </div>

    <!-- MODAL TAMBAH STOK -->
    <div id="addStockModal" class="modal-overlay">
        <div class="modal-container bg-white rounded-3xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <span class="bg-[#c9973a] text-white text-xs font-bold tracking-wider px-5 py-1 rounded-full">TAMBAH STOK</span>
            </div>
            <h2 class="font-playfair text-2xl font-bold text-center text-[#1a1208] mt-2 mb-6">Tambah Stok</h2>
            <form id="addStockForm" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="add_product_id">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Produk</label>
                    <div class="bg-[#f3e4c3] rounded-xl px-4 py-3"><span id="add_product_name" class="text-sm text-[#3a3020]"></span></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Stok Saat Ini</label>
                    <div class="bg-gray-100 rounded-xl px-4 py-3"><span id="add_current_stock" class="text-sm text-gray-600">0</span></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="add_quantity" required min="1" class="w-full px-4 py-3 border border-[#e8d5a8] rounded-xl focus:outline-none focus:border-[#c9973a]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="description" id="add_description" rows="3" required class="w-full px-4 py-3 border border-[#e8d5a8] rounded-xl focus:outline-none focus:border-[#c9973a]" placeholder="Contoh: Pembelian dari supplier PT Maju Jaya"></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Stok
                </button>
                <div class="text-center mt-4">
                    <button type="button" onclick="closeModal('addStockModal')" class="text-[#9a8060] text-sm hover:text-[#7a5c1e]">Tutup</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KURANG STOK (RADIO BUTTON) -->
    <div id="removeStockModal" class="modal-overlay">
        <div class="modal-container bg-white rounded-3xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <span class="bg-red-500 text-white text-xs font-bold tracking-wider px-5 py-1 rounded-full">KURANG STOK</span>
            </div>
            <h2 class="font-playfair text-2xl font-bold text-center text-[#1a1208] mt-2 mb-6">Kurang Stok</h2>
            <form id="removeStockForm" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="remove_product_id">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Produk</label>
                    <div class="bg-[#f3e4c3] rounded-xl px-4 py-3"><span id="remove_product_name" class="text-sm text-[#3a3020]"></span></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Stok Saat Ini</label>
                    <div class="bg-gray-100 rounded-xl px-4 py-3"><span id="remove_current_stock" class="text-sm text-gray-600">0</span></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="remove_quantity" required min="1" class="w-full px-4 py-3 border border-[#e8d5a8] rounded-xl focus:outline-none focus:border-[#c9973a]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-3">Kondisi Barang <span class="text-red-500">*</span></label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="condition" value="good" checked class="w-4 h-4 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700">Aman</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="condition" value="damaged" class="w-4 h-4 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">Rusak</span>
                        </label>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#3a3020] mb-2">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="description" id="remove_description" rows="3" required class="w-full px-4 py-3 border border-[#e8d5a8] rounded-xl focus:outline-none focus:border-[#c9973a]" placeholder="Contoh: Barang keluar untuk customer"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-all duration-200">
                    <i class="fas fa-minus mr-2"></i> Kurang Stok
                </button>
                <div class="text-center mt-4">
                    <button type="button" onclick="closeModal('removeStockModal')" class="text-[#9a8060] text-sm hover:text-[#7a5c1e]">Tutup</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HISTORI -->
    <div id="historyModal" class="modal-overlay">
        <div class="modal-history-container bg-white rounded-2xl p-5 mx-4 shadow-2xl">
            <div class="text-center mb-3">
                <span class="bg-blue-500 text-white text-xs font-bold tracking-wider px-4 py-1 rounded-full">HISTORI STOK</span>
            </div>
            <h2 id="historyProductName" class="font-playfair text-xl font-bold text-center text-[#1a1208] mt-1 mb-1">Histori Barang</h2>
            <p id="historyProductCode" class="text-center text-gray-500 text-xs mb-4"></p>
            <div class="overflow-x-auto max-h-[55vh] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-[#f3e4c3]">
                        <tr>
                            <th class="rounded-l-xl px-3 py-2 text-left text-xs font-semibold text-[#7a5c1e]">No</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-[#7a5c1e]">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-[#7a5c1e]">User</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-[#7a5c1e]">Jenis</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-[#7a5c1e]">Jumlah</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-[#7a5c1e]">Kondisi</th>
                            <th class="rounded-r-xl px-3 py-2 text-left text-xs font-semibold text-[#7a5c1e]">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <tr><td colspan="7" class="text-center py-8 text-gray-400">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-4">
                <button onclick="closeModal('historyModal')" class="text-[#9a8060] text-sm hover:text-[#7a5c1e] transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL BARANG -->
    <div id="detailModal" class="modal-overlay">
        <div class="modal-container bg-white rounded-3xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <span class="bg-gray-500 text-white text-xs font-bold tracking-wider px-5 py-1 rounded-full">DETAIL BARANG</span>
            </div>
            <div class="flex justify-center mb-4">
                <img id="detailImage" src="" alt="Product" class="w-32 h-32 object-cover rounded-xl">
            </div>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2 font-semibold">Kode Produk</td><td class="py-2" id="detailCode">-</td></tr>
                <tr class="border-b"><td class="py-2 font-semibold">Nama Produk</td><td class="py-2" id="detailName">-</td></tr>
                <tr class="border-b"><td class="py-2 font-semibold">Kategori</td><td class="py-2" id="detailCategory">-</td></tr>
                <tr class="border-b"><td class="py-2 font-semibold">Stok Saat Ini</td><td class="py-2" id="detailStock">-</td></tr>
                <tr class="border-b"><td class="py-2 font-semibold align-top">Deskripsi</td><td class="py-2" id="detailDescription">-</td></tr>
            </table>
            <div class="text-center mt-4">
                <button onclick="closeModal('detailModal')" class="text-[#9a8060] text-sm hover:text-[#7a5c1e]">Tutup</button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast-notification" class="fixed top-5 right-5 z-50 hidden">
        <div class="px-6 py-3 rounded-xl shadow-lg text-white text-sm font-semibold flex items-center gap-3 bg-green-500">
            <i id="toast-icon" class="fas fa-check-circle"></i>
            <span id="toast-message"></span>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .modal-container { animation: modalFadeIn 0.3s ease-out; max-width: 450px; width: 90%; }
        .modal-history-container { max-width: 850px; width: 90%; max-height: 85vh; display: flex; flex-direction: column; }
        @media (max-width: 768px) { .modal-history-container { max-width: 95%; padding: 1rem; } }
        .modal-history-container .overflow-y-auto::-webkit-scrollbar { width: 6px; height: 6px; }
        .modal-history-container .overflow-y-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .modal-history-container .overflow-y-auto::-webkit-scrollbar-thumb { background: #c9973a; border-radius: 10px; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .stock-zero { background-color: #fee2e2; color: #dc2626; font-weight: bold; }
        .stock-warning { background-color: #fed7aa; color: #c2410c; font-weight: bold; }
        .stock-normal { background-color: #dcfce7; color: #16a34a; font-weight: bold; }
        #toast-notification.show { display: block; animation: slideInRight 0.3s ease-out; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-notification');
            const icon = document.getElementById('toast-icon');
            const messageSpan = document.getElementById('toast-message');
            const toastDiv = toast.querySelector('div');
            if (type === 'success') {
                toastDiv.className = 'px-6 py-3 rounded-xl shadow-lg text-white text-sm font-semibold flex items-center gap-3 bg-green-500';
                icon.className = 'fas fa-check-circle';
            } else {
                toastDiv.className = 'px-6 py-3 rounded-xl shadow-lg text-white text-sm font-semibold flex items-center gap-3 bg-red-500';
                icon.className = 'fas fa-exclamation-triangle';
            }
            messageSpan.innerText = message;
            toast.classList.remove('hidden');
            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); toast.classList.add('hidden'); }, 3000);
        }

        function getStockHistoryUrl(productId) { return '/contents/stock/history/' + productId; }
        function getStockDetailUrl(productId) { return '/contents/stock/detail/' + productId; }
        function getStockAddUrl() { return '/contents/stock/add'; }
        function getStockRemoveUrl() { return '/contents/stock/remove'; }

        function openModal(modalId) { document.getElementById(modalId).style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; document.body.style.overflow = 'auto'; }

        function openAddStockModal(productId, productName, currentStock) {
            document.getElementById('add_product_id').value = productId;
            document.getElementById('add_product_name').innerText = productName;
            document.getElementById('add_current_stock').innerText = currentStock;
            document.getElementById('add_quantity').value = '';
            document.getElementById('add_description').value = '';
            openModal('addStockModal');
        }

        function openRemoveStockModal(productId, productName, currentStock) {
            if (currentStock <= 0) { showToast('Stok habis! Tidak bisa mengurangi stok.', 'error'); return; }
            document.getElementById('remove_product_id').value = productId;
            document.getElementById('remove_product_name').innerText = productName;
            document.getElementById('remove_current_stock').innerText = currentStock;
            document.getElementById('remove_quantity').value = '';
            document.getElementById('remove_description').value = '';
            const radioGood = document.querySelector('input[name="condition"][value="good"]');
            if (radioGood) radioGood.checked = true;
            openModal('removeStockModal');
        }

        async function showHistory(productId) {
            openModal('historyModal');
            document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Loading...</td></tr>';
            try {
                const response = await fetch(getStockHistoryUrl(productId), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('historyProductName').innerText = data.product.name;
                    document.getElementById('historyProductCode').innerText = 'Kode: ' + data.product.code;
                    if (data.transactions.length === 0) {
                        document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Belum ada histori transaksi</td></tr>';
                    } else {
                        let html = '';
                        data.transactions.forEach((t, idx) => {
                            const jenisClass = t.jenis === 'Masuk' ? 'text-green-600' : 'text-red-600';
                            const kondisiClass = t.kondisi === 'Aman' ? 'text-green-600' : 'text-orange-600';
                            html += `<tr class="border-b"><td class="px-3 py-2">${idx+1}</td><td class="px-3 py-2">${t.tanggal}</td><td class="px-3 py-2">${t.user}</td><td class="px-3 py-2 text-center ${jenisClass}">${t.jenis}</td><td class="px-3 py-2 text-center">${t.jumlah}</td><td class="px-3 py-2 text-center ${kondisiClass}">${t.kondisi}</td><td class="px-3 py-2">${t.deskripsi}</td></tr>`;
                        });
                        document.getElementById('historyTableBody').innerHTML = html;
                    }
                } else { document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-400">Gagal memuat data</td></tr>'; }
            } catch (error) { document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-400">Error: ' + error.message + '</td></tr>'; showToast('Gagal memuat histori', 'error'); }
        }

        async function showDetail(productId) {
            openModal('detailModal');
            try {
                const response = await fetch(getStockDetailUrl(productId), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('detailCode').innerText = data.product.code;
                    document.getElementById('detailName').innerText = data.product.name;
                    document.getElementById('detailCategory').innerText = data.product.category;
                    document.getElementById('detailStock').innerText = data.product.stock + ' unit';
                    document.getElementById('detailDescription').innerText = data.product.description || '-';
                    const img = document.getElementById('detailImage');
                    img.src = data.product.image_url;
                    img.onerror = () => { img.src = '{{ asset('images/placeholder.png') }}'; };
                }
            } catch (error) { showToast('Gagal memuat detail produk', 'error'); closeModal('detailModal'); }
        }

        document.getElementById('addStockForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch(getStockAddUrl(), { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: formData });
                const result = await response.json();
                if (result.success) { showToast(result.message, 'success'); closeModal('addStockModal'); setTimeout(() => location.reload(), 1500); } 
                else { showToast(result.message, 'error'); }
            } catch (error) { showToast('Terjadi kesalahan', 'error'); }
        });

        document.getElementById('removeStockForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch(getStockRemoveUrl(), { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: formData });
                const result = await response.json();
                if (result.success) { showToast(result.message, 'success'); closeModal('removeStockModal'); setTimeout(() => location.reload(), 1500); } 
                else { showToast(result.message, 'error'); }
            } catch (error) { showToast('Terjadi kesalahan', 'error'); }
        });

        function applyFilter() {
            const search = document.getElementById('search-input').value;
            const category = document.getElementById('category-filter').value;
            let url = window.location.pathname + '?';
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (category) params.push('category_id=' + category);
            window.location.href = url + params.join('&');
        }
        document.getElementById('search-input')?.addEventListener('keypress', function(e) { if (e.key === 'Enter') applyFilter(); });
        document.getElementById('category-filter')?.addEventListener('change', applyFilter);
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam && document.getElementById('search-input')) { document.getElementById('search-input').value = searchParam; }
        window.onclick = function(event) { if (event.target.classList.contains('modal-overlay')) { event.target.style.display = 'none'; document.body.style.overflow = 'auto'; } }
        @if (session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if (session('error')) showToast('{{ session('error') }}', 'error'); @endif
    </script>
@endsection