@extends('layout.content')

@section('title', 'Categories')

@section('content')
    <div class="flex-1 p-4 sm:p-6">

        <div class="bg-white p-4 sm:p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Categories</h2>
            <p class="text-[#8b7a66] text-sm sm:text-base mt-1">Kelola kategori produk furniture Anda</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4 shadow-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4 shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- SEARCH & TAMBAH CATEGORY -->
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <form method="GET" action="{{ route('contents.categories') }}" class="flex gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a]" width="14" height="14"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" placeholder="Cari kategori..." value="{{ request('search') }}"
                        class="border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] transition-colors focus:border-[#c9973a] focus:outline-none w-48 sm:w-64">
                </div>
                <button type="submit"
                    class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-4 py-2 rounded-xl transition-colors">
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
                @if (request('search'))
                    <a href="{{ route('contents.categories') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition shadow-sm">
                        Reset
                    </a>
                @endif
            </form>

            <button onclick="openAddModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-5 py-2 rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus text-sm"></i>
                <span class="text-sm">Tambah Kategori</span>
            </button>
        </div>

        <!-- CATEGORY LIST TABLE -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-[#e7ddcf]">
            <div class="px-4 sm:px-6 py-4 border-b border-[#e7ddcf] bg-[#faf6ef]">
                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-list text-[#cbb892]"></i> Category List
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-hover">
                    <thead class="bg-[#e7ddcf] text-gray-700">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">ID</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Kategori</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $category)
                            <tr class="hover:bg-[#fef7e8] transition">
                                <td class="px-4 sm:px-6 py-3 text-sm text-gray-600 text-left">{{ $category->id }}</td>
                                <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-800 text-left">{{ $category->name }}</td>
                                <td class="px-4 sm:px-6 py-3 text-left">
                                    <button type="button" data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                        onclick="openEditModal(this)"
                                        class="text-blue-500 hover:text-blue-700 transition mr-2 sm:mr-3" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" data-id="{{ $category->id }}" onclick="openDeleteModal(this)"
                                        class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 sm:px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                                    No data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $categories->links() }}
        </div>

    </div>

    <!-- MODAL ADD CATEGORY - RESPONSIF -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-plus-circle text-[#8faa7b]"></i> Tambah Kategori
                </h3>
                <button onclick="closeAddModal()" class="modal-close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('contents.categories.store') }}">
                @csrf
                <div class="modal-body">
                    <label class="modal-label">Nama Kategori</label>
                    <input type="text" name="name" placeholder="Masukkan nama kategori"
                        class="modal-input" required>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn-cancel">
                        Batal
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT CATEGORY - RESPONSIF -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-edit text-blue-500"></i> Edit Kategori
                </h3>
                <button onclick="closeEditModal()" class="modal-close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <label class="modal-label">Nama Kategori</label>
                    <input type="text" name="name" id="editName" placeholder="Masukkan nama kategori"
                        class="modal-input" required>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">
                        Batal
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DELETE CONFIRMATION - RESPONSIF -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-container-sm">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-red-500"></i> Konfirmasi Hapus
                </h3>
                <button onclick="closeDeleteModal()" class="modal-close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="delete-icon">
                    <i class="fas fa-trash-alt text-red-500 text-5xl mb-3"></i>
                </div>
                <p class="text-gray-600 mb-2">Yakin ingin menghapus kategori ini?</p>
                <p class="text-red-500 text-sm">
                    <i class="fas fa-info-circle"></i> Kategori tidak bisa dihapus jika masih memiliki produk!
                </p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()" class="btn-cancel">
                        Batal
                    </button>
                    <button type="submit" class="btn-delete">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Modal Styles */
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

        .modal-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .modal-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .modal-input:focus {
            outline: none;
            border-color: #c9973a;
            ring: 2px solid rgba(201, 151, 58, 0.2);
        }

        .btn-cancel {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            color: #6b7280;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #f9fafb;
        }

        .btn-save {
            padding: 0.5rem 1.25rem;
            background: #8faa7b;
            border: none;
            border-radius: 0.75rem;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-save:hover {
            background: #7a9666;
        }

        .btn-delete {
            padding: 0.5rem 1.25rem;
            background: #ef4444;
            border: none;
            border-radius: 0.75rem;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .delete-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .table-hover tbody tr:hover {
            background-color: #fef7e8;
        }

        /* Responsive */
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
            .btn-cancel, .btn-save, .btn-delete {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // Add Modal functions
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Edit Modal functions
        function openEditModal(button) {
            let id = button.dataset.id;
            let name = button.dataset.name;

            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('editName').value = name;
            document.body.style.overflow = 'hidden';

            let form = document.getElementById('editForm');
            form.action = "{{ route('contents.categories.update', ['id' => ':id']) }}".replace(':id', id);
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Delete Modal functions
        function openDeleteModal(button) {
            let id = button.getAttribute('data-id');

            document.getElementById('deleteModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';

            let form = document.getElementById('deleteForm');
            form.action = "{{ route('contents.categories.destroy', ['id' => ':id']) }}".replace(':id', id);
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');

            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        }

        // Auto hide success message after 3 seconds
        setTimeout(function() {
            const successMsg = document.querySelector('.bg-green-100');
            const errorMsg = document.querySelector('.bg-red-100');
            if (successMsg) {
                setTimeout(function() {
                    successMsg.style.display = 'none';
                }, 3000);
            }
            if (errorMsg && !errorMsg.querySelector('ul')) {
                setTimeout(function() {
                    errorMsg.style.display = 'none';
                }, 3000);
            }
        }, 100);
    </script>
@endsection