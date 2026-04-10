<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Categories | Luxury Furniture</title>
  @vite('resources/css/app.css')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .modal-transition {
      transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
      background-color: #fef7e8;
    }
  </style>
</head>

<body class="bg-[#f4efe3] font-sans">

  <div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <div class="w-64 bg-[#cbb892] p-6 shadow-lg">
      <div class="flex items-center gap-2 mb-8 border-b border-[#a88454] pb-4">
        <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-72">
      </div>

      <div class="space-y-2">
        <a href="{{ route('contents.dashboard') }}" class="flex items-center gap-3 p-3 rounded-lg text-[#2c2b26] hover:bg-white/70 transition">
          <i class="fas fa-tachometer-alt w-5"></i> Dashboard
        </a>
        <a href="{{ route('admin.manage-admin') }}" class="flex items-center gap-3 p-3 rounded-lg text-[#2c2b26] hover:bg-white/70 transition">
          <i class="fas fa-user-cog w-5"></i> Manage Account Admin
        </a>
        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-lg text-[#2c2b26] hover:bg-white/70 transition">
          <i class="fas fa-users w-5"></i> User Management
        </a>
        <a href="#" class="flex items-center gap-3 bg-white/90 p-3 rounded-lg font-semibold text-[#3a2c1f] shadow-sm">
          <i class="fas fa-tags w-5"></i> Category
        </a>
        <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 p-3 rounded-lg text-[#2c2b26] hover:bg-white/70 transition">
          <i class="fas fa-chart-line w-5"></i> Report
        </a>
      </div>

      <div class="absolute bottom-6 left-6 right-6">
        <form action="{{ url('/logout') }}" method="POST" class="w-full">
          @csrf
          <button type="submit" class="flex items-center gap-3 p-3 rounded-lg text-red-800 hover:bg-red-100 transition poin">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Log Out</span>
          </button>
        </form>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 p-6">

      <!-- Header Halaman -->
      <div class="bg-white p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
        <h2 class="text-2xl font-bold text-gray-800">Categories</h2>
        <p class="text-[#8b7a66] mt-1">Kelola kategori produk furniture Anda</p>
      </div>

      <!-- SUCCESS MESSAGE (LOGIKA ASLI) -->
      @if(session('success'))
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4 shadow-sm">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
      </div>
      @endif

      <!-- VALIDATION ERROR (LOGIKA ASLI) -->
      @if($errors->any())
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4 shadow-sm">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <!-- SEARCH BAR & ADD BUTTON - KEDUANYA DI KIRI -->
      <div class="flex flex-wrap items-center gap-4 mb-6">
        <form method="GET" action="{{ route('admin.categories') }}" class="flex gap-2">
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
              type="text"
              name="search"
              placeholder="Search Categories..."
              value="{{ request('search') }}"
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl w-64 focus:outline-none focus:ring-2 focus:ring-[#cbb892] focus:border-transparent">
          </div>
          <button type="submit" class="bg-[#cbb892] hover:bg-[#b89a6a] text-white px-5 py-2 rounded-xl transition shadow-sm">
            <i class="fas fa-search mr-1"></i> Search
          </button>
        </form>

        <button onclick="openModal()" class="bg-[#8faa7b] hover:bg-[#7a9666] text-white px-5 py-2 rounded-xl transition shadow-sm flex items-center gap-2">
          <i class="fas fa-plus"></i> Add Category
        </button>
      </div>

      <!-- CATEGORY LIST TABLE -->
      <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-[#e7ddcf]">
        <div class="px-6 py-4 border-b border-[#e7ddcf] bg-[#faf6ef]">
          <h3 class="font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-list text-[#cbb892]"></i> Category List
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full table-hover">
            <thead class="bg-[#e7ddcf] text-gray-700">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Kategori</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse($categories as $category)
              <tr class="hover:bg-[#fef7e8] transition">
                <td class="px-6 py-3 text-sm text-gray-600 text-left">{{ $category->id }}</td>
                <td class="px-6 py-3 text-sm font-medium text-gray-800 text-left">{{ $category->name }}</td>
                <td class="px-6 py-3 text-left">
                  <!-- EDIT BUTTON (LOGIKA ASLI) -->
                  <button
                    type="button"
                    data-id="{{ $category->id }}"
                    data-name="{{ $category->name }}"
                    onclick="openEditModal(this)"
                    class="text-blue-500 hover:text-blue-700 transition mr-3"
                    title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  <!-- DELETE BUTTON (LOGIKA ASLI) -->
                  <button
                    type="button"
                    data-id="{{ $category->id }}"
                    onclick="openDeleteModal(this)"
                    class="text-red-500 hover:text-red-700 transition"
                    title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                  <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                  No data found
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- PAGINATION (LOGIKA ASLI) -->
      <div class="mt-6">
        {{ $categories->links() }}
      </div>

    </div>
  </div>

  <!-- MODAL ADD CATEGORY (DESAIN ELEGAN, LOGIKA ASLI) -->
  <div id="categoryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;" class="flex">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all modal-transition">
      <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
          <i class="fas fa-plus-circle text-[#8faa7b]"></i> Add Category
        </h3>
        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="px-6 py-5">
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
          <input
            type="text"
            name="name"
            placeholder="Add New Category"
            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#cbb892] focus:border-transparent"
            required>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
          <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
            Cancel
          </button>
          <button type="submit" class="px-5 py-2 bg-[#8faa7b] hover:bg-[#7a9666] text-white rounded-lg transition shadow-sm flex items-center gap-2">
            <i class="fas fa-save"></i> Add
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT CATEGORY (DESAIN ELEGAN, LOGIKA ASLI) -->
  <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;" class="flex">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all modal-transition">
      <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
          <i class="fas fa-edit text-blue-500"></i> Edit Category
        </h3>
        <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="px-6 py-5">
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
          <input type="text" name="name" id="editName" required
            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#cbb892] focus:border-transparent">
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
          <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
            Cancel
          </button>
          <button type="submit" onclick="disableButton(this)" class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition shadow-sm flex items-center gap-2">
            <i class="fas fa-save"></i> Update
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL DELETE CONFIRMATION (DESAIN ELEGAN, LOGIKA ASLI) -->
  <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;" class="flex">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 transform transition-all modal-transition">
      <div class="border-b border-gray-100 px-6 py-4">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
          <i class="fas fa-exclamation-triangle text-red-500"></i> Konfirmasi
        </h3>
      </div>
      <div class="px-6 py-4">
        <p class="text-gray-600">Yakin hapus kategori ini?</p>
      </div>
      <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
            Batal
          </button>
          <button type="submit" class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition shadow-sm">
            Ya, Hapus
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('categoryModal').style.display = 'none';
    }

    function openEditModal(button) {
      let id = button.dataset.id;
      let name = button.dataset.name;

      document.getElementById('editModal').style.display = 'flex';
      document.getElementById('editName').value = name;

      let form = document.getElementById('editForm');
      form.action = '/admin/categories/' + id;
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    function openDeleteModal(button) {
      let id = button.getAttribute('data-id');

      document.getElementById('deleteModal').style.display = 'flex';

      let form = document.getElementById('deleteForm');
      form.action = '/admin/categories/' + id;
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').style.display = 'none';
    }

    function disableButton(btn) {
      btn.disabled = true;
      btn.innerText = 'Processing...';
      btn.form.submit();
    }

    window.onclick = function(event) {
      const addModal = document.getElementById('categoryModal');
      const editModal = document.getElementById('editModal');
      const deleteModal = document.getElementById('deleteModal');

      if (event.target === addModal) {
        closeModal();
      }
      if (event.target === editModal) {
        closeEditModal();
      }
      if (event.target === deleteModal) {
        closeDeleteModal();
      }
    }
  </script>

  @if($errors->any())
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      openModal();
    });
  </script>
  @endif


</body>

</html>