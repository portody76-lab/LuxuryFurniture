@extends('layout.content')

@section('title', 'User Management')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">User Management</h1>
            <p class="text-gray-500 mt-1">Kelola semua user (Admin & Operator)</p>
        </div>

        <!-- STATISTIK CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <!-- Total Users -->
            <div class="bg-linear-to-r from-blue-50 to-blue-100/50 rounded-2xl p-5 border border-blue-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 uppercase tracking-wide">Total User</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalUsers }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Admin -->
            <div class="bg-linear-to-r from-green-50 to-green-100/50 rounded-2xl p-5 border border-green-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 uppercase tracking-wide">Admin</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAdmin }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-shield text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Operator -->
            <div class="bg-linear-to-r from-amber-50 to-amber-100/50 rounded-2xl p-5 border border-amber-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 uppercase tracking-wide">Operator</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalOperator }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-cog text-amber-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- SEARCH & TAMBAH USER -->
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <form method="GET" action="{{ route('contents.admin.users') }}" class="flex gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" placeholder="Cari username atau email..."
                        value="{{ request('search') }}"
                        class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl w-64 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition">
                </div>
                <button type="submit"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl transition shadow-sm">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('contents.admin.users') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-xl transition shadow-sm">
                        Reset
                    </a>
                @endif
            </form>

            <button onclick="openAddModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        <!-- TABLE USER -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">NO.</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Username</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Role</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 {{ $user->role_id == 1 ? 'bg-green-100' : 'bg-amber-100' }} rounded-full flex items-center justify-center">
                                            <i
                                                class="fas fa-user {{ $user->role_id == 1 ? 'text-green-600' : 'text-amber-600' }} text-sm"></i>
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $user->username }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->role_id == 1 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        <i
                                            class="fas {{ $user->role_id == 1 ? 'fa-user-shield' : 'fa-user-cog' }} mr-1 text-xs"></i>
                                        {{ $user->role->role_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="flex items-center gap-1 text-sm {{ $user->status ? 'text-green-600' : 'text-red-600' }}">
                                        <i class="fas fa-circle text-[8px]"></i>
                                        {{ $user->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- TOMBOL TOGGLE STATUS (AKTIF/NONAKTIF) -->
                                        <button
                                            onclick="openToggleModal({{ $user->id }}, '{{ $user->username }}', {{ $user->status }})"
                                            class="{{ $user->status ? 'text-green-600 hover:text-green-800' : 'text-gray-500 hover:text-gray-700' }} transition"
                                            title="{{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas {{ $user->status ? 'fa-toggle-on' : 'fa-toggle-off' }} text-2xl"></i>
                                        </button>

                                        <button
                                            onclick="openEditModal({{ $user->id }}, '{{ $user->username }}', '{{ $user->email }}', {{ $user->role_id }})"
                                            class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>

                                        <button onclick="openResetModal({{ $user->id }}, '{{ $user->username }}')"
                                            class="text-teal-600 hover:text-teal-800 transition" title="Reset Password">
                                            <i class="fas fa-key text-lg"></i>
                                        </button>

                                        <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->username }}')"
                                            class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-4xl mb-2 block"></i>
                                    Tidak ada data user
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH USER -->
    <div id="addModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
            <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-plus text-green-500 text-xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">Tambah User Baru</h3>
                </div>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('contents.admin.users.store') }}">
                @csrf

                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition"
                            placeholder="Masukkan username">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition"
                            placeholder="contoh: user@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                        <select name="role_id" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-amber-50 rounded-xl p-3 text-sm text-amber-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password default: <strong>12345678</strong>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                    <button type="button" onclick="closeAddModal()"
                        class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT USER -->
    <div id="editModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
            <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-edit text-blue-500 text-xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">Edit User</h3>
                </div>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" id="edit_username" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="edit_email" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                        <select name="role_id" id="edit_role_id" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition shadow-sm">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL RESET PASSWORD -->
    <div id="resetModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
            <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-key text-teal-500 text-xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">Reset Password</h3>
                </div>
                <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="px-6 py-6">
                <p class="text-gray-600">Yakin ingin reset password user <strong id="reset_username"></strong>?</p>
                <div class="bg-amber-50 rounded-xl p-3 mt-4 text-sm text-amber-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Password akan direset menjadi: <strong>12345678</strong>
                </div>
            </div>

            <form id="resetForm" method="POST">
                @csrf
                @method('PUT')
                <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                    <button type="button" onclick="closeResetModal()"
                        class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl transition shadow-sm">Ya,
                        Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TOGGLE STATUS (AKTIF/NONAKTIF) -->
    <div id="toggleModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
            <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-power-off text-amber-500 text-xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">Ubah Status User</h3>
                </div>
                <button onclick="closeToggleModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="px-6 py-6">
                <p class="text-gray-600">
                    Yakin ingin <strong id="toggle_action"></strong> user
                    <strong id="toggle_username"></strong>?
                </p>
                <p class="text-sm mt-2" id="toggle_message">
                </p>
            </div>

            <form id="toggleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                    <button type="button" onclick="closeToggleModal()"
                        class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl transition shadow-sm">Ya,
                        Ubah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS USER -->
    <div id="deleteModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
            <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-trash-alt text-red-500 text-xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">Hapus User</h3>
                </div>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="px-6 py-6">
                <p class="text-gray-600">Yakin ingin menghapus user <strong id="delete_username"></strong>?</p>
                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition shadow-sm">Ya,
                        Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.2s ease-out;
        }
    </style>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addModal').classList.remove('flex');
        }

        function openEditModal(id, username, email, roleId) {
            document.getElementById('editForm').action = '/contents/users/' + id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role_id').value = roleId;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        function openResetModal(id, username) {
            document.getElementById('resetForm').action = '/contents/users/' + id + '/reset-password';
            document.getElementById('reset_username').innerText = username;
            document.getElementById('resetModal').classList.remove('hidden');
            document.getElementById('resetModal').classList.add('flex');
        }

        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            document.getElementById('resetModal').classList.remove('flex');
        }

        // TOGGLE STATUS MODAL
        function openToggleModal(id, username, currentStatus) {
            // currentStatus: 1 = aktif, 0 = nonaktif
            var action = currentStatus == 1 ? 'nonaktifkan' : 'aktifkan';
            var statusNote = '';
            var messageHtml = '';

            if (currentStatus == 1) {
                // Sedang AKTIF -> akan dinonaktifkan
                messageHtml = '<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i> ' +
                    'User yang <strong>dinonaktifkan</strong> tidak akan bisa login.';
            } else {
                // Sedang NONAKTIF -> akan diaktifkan
                messageHtml = '<i class="fas fa-check-circle text-green-500 mr-1"></i> ' +
                    'User yang <strong>diaktifkan</strong> akan bisa mengakses halamannya kembali.';
            }

            document.getElementById('toggle_action').innerText = action;
            document.getElementById('toggle_username').innerText = username;
            document.getElementById('toggle_message').innerHTML = messageHtml;
            document.getElementById('toggleForm').action = '/contents/users/' + id + '/toggle-status';
            document.getElementById('toggleModal').classList.remove('hidden');
            document.getElementById('toggleModal').classList.add('flex');
        }

        function closeToggleModal() {
            document.getElementById('toggleModal').classList.add('hidden');
            document.getElementById('toggleModal').classList.remove('flex');
        }

        function openDeleteModal(id, username) {
            document.getElementById('deleteForm').action = '/contents/users/' + id;
            document.getElementById('delete_username').innerText = username;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        window.onclick = function (event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const resetModal = document.getElementById('resetModal');
            const deleteModal = document.getElementById('deleteModal');
            const toggleModal = document.getElementById('toggleModal');

            if (event.target === addModal) closeAddModal();
            if (event.target === editModal) closeEditModal();
            if (event.target === resetModal) closeResetModal();
            if (event.target === deleteModal) closeDeleteModal();
            if (event.target === toggleModal) closeToggleModal();
        }
    </script>
@endsection