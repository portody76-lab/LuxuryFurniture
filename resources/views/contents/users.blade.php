@extends('layout.content')

@section('title', 'User Management')

@section('content')
    {{-- TAMBAHKAN overflow-x-hidden di container utama --}}
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 py-4 sm:py-8 overflow-x-hidden">

        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight wrap-break-words">Manajemen User</h1>
            <p class="text-sm sm:text-base text-gray-500 mt-1 wrap-break-words">Kelola semua user (Admin & Operator)</p>
        </div>

        <!-- STATISTIK CARDS -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-5 mb-6 sm:mb-8">
            <div
                class="bg-linear-to-r from-blue-50 to-blue-100/50 rounded-2xl p-4 sm:p-5 border border-blue-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-blue-600 uppercase tracking-wide">Total User</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 wrap-break-words">
                            {{ $totalUsers }}</h3>
                    </div>
                    <div
                        class="w-9 h-9 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center shrink-0 ml-2">
                        <i class="fas fa-users text-blue-500 text-base sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-linear-to-r from-purple-50 to-purple-100/50 rounded-2xl p-4 sm:p-5 border border-purple-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-purple-600 uppercase tracking-wide">Super Admin</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 wrap-break-words">
                            {{ $totalSuperAdmin }}</h3>
                    </div>
                    <div
                        class="w-9 h-9 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center shrink-0 ml-2">
                        <i class="fas fa-crown text-purple-500 text-base sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-linear-to-r from-green-50 to-green-100/50 rounded-2xl p-4 sm:p-5 border border-green-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-green-600 uppercase tracking-wide">Admin</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 wrap-break-words">
                            {{ $totalAdmin }}</h3>
                    </div>
                    <div
                        class="w-9 h-9 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center shrink-0 ml-2">
                        <i class="fas fa-user-shield text-green-500 text-base sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-linear-to-r from-amber-50 to-amber-100/50 rounded-2xl p-4 sm:p-5 border border-amber-200/50 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-amber-600 uppercase tracking-wide">Operator</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 wrap-break-words">
                            {{ $totalOperator }}</h3>
                    </div>
                    <div
                        class="w-9 h-9 sm:w-12 sm:h-12 bg-amber-100 rounded-xl flex items-center justify-center shrink-0 ml-2">
                        <i class="fas fa-user-cog text-amber-500 text-base sm:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERT MESSAGES -->

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 rounded-xl mb-4 sm:mb-6 shadow-sm">
                <div class="flex items-center gap-2 text-sm sm:text-base wrap-break-words">
                    <i class="fas fa-exclamation-circle text-red-500 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 rounded-xl mb-4 sm:mb-6 shadow-sm">
                <div class="flex items-start gap-2 text-sm wrap-break-words">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 shrink-0"></i>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- SEARCH & TAMBAH USER -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 mb-6">
            <form method="GET" action="{{ route('contents.users') }}" class="flex gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64 sm:flex-none">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#c9973a]" width="14" height="14" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" placeholder="Cari username atau email..."
                        value="{{ request('search') }}"
                        class="w-full border border-[#e8d5a8] rounded-xl py-2 pl-9 pr-4 text-sm bg-[#fdf8f0] transition-colors focus:border-[#c9973a] focus:outline-none">
                </div>
                <button type="submit"
                    class="bg-[#c9973a] hover:bg-[#b07e28] text-white px-4 py-2 rounded-xl transition-colors flex items-center justify-center shrink-0">
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <span class="ml-1 sm:hidden">Cari</span>
                </button>
                @if (request('search'))
                    <a href="{{ route('contents.users') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition shadow-sm text-center shrink-0">
                        Reset
                    </a>
                @endif
            </form>

            <button onclick="openAddModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                <i class="fas fa-plus"></i> <span>Tambah User</span>
            </button>
        </div>

        <!-- TABLE USER - PERBAIKAN OVERFLOW -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full min-w-125 md:min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-600">
                                NO.</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-600">
                                Username</th>
                            <th
                                class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-600 hidden sm:table-cell">
                                Email</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-600">
                                Role</th>
                            <th
                                class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-600 hidden md:table-cell">
                                Status</th>
                            <th
                                class="px-3 sm:px-6 py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold text-gray-600">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center shrink-0 {{ $user->role_id == 3 ? 'bg-purple-100' : ($user->role_id == 1 ? 'bg-green-100' : 'bg-amber-100') }}">
                                            <i
                                                class="fas fa-user text-xs {{ $user->role_id == 3 ? 'text-purple-600' : ($user->role_id == 1 ? 'text-green-600' : 'text-amber-600') }}"></i>
                                        </div>
                                        <span
                                            class="font-medium text-gray-800 text-sm sm:text-base wrap-break-words">{{ $user->username }}</span>
                                    </div>
                                </td>
                                <td
                                    class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500 hidden sm:table-cell wrap-break-words">
                                    {{ $user->email ?? '-' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span
                                        class="inline-flex px-2 sm:px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $user->role_id == 3 ? 'bg-purple-100 text-purple-700' : ($user->role_id == 1 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700') }}">
                                        <i
                                            class="fas {{ $user->role_id == 3 ? 'fa-crown' : ($user->role_id == 1 ? 'fa-user-shield' : 'fa-user-cog') }} mr-1 text-xs"></i>
                                        <span class="hidden sm:inline">{{ $user->role->role_name ?? '-' }}</span>
                                        <span
                                            class="sm:hidden">{{ $user->role_id == 3 ? 'Super' : ($user->role_id == 1 ? 'Admin' : 'Op') }}</span>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 hidden md:table-cell">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs sm:text-sm whitespace-nowrap {{ $user->status ? 'text-green-600' : 'text-red-600' }}">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ $user->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                    @if($user->role_id == 3)
                                        <!-- Super Admin - Tampilkan badge "Tidak Dapat Dikelola" -->
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                            <i class="fas fa-lock"></i> Tidak Dapat Dikelola
                                        </span>
                                    @else
                                        <div class="flex items-center justify-center gap-1 sm:gap-2 flex-wrap">
                                            <button
                                                onclick="openToggleModal({{ $user->id }}, '{{ $user->username }}', {{ $user->status }})"
                                                class="{{ $user->status ? 'text-green-600 hover:text-green-800' : 'text-gray-500 hover:text-gray-700' }} transition p-1"
                                                title="{{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i
                                                    class="fas {{ $user->status ? 'fa-toggle-on' : 'fa-toggle-off' }} text-lg sm:text-2xl"></i>
                                            </button>
                                            <button
                                                onclick="openEditModal({{ $user->id }}, '{{ $user->username }}', '{{ $user->email }}', {{ $user->role_id }})"
                                                class="text-blue-600 hover:text-blue-800 transition p-1" title="Edit">
                                                <i class="fas fa-edit text-sm sm:text-lg"></i>
                                            </button>
                                            <button onclick="openResetModal({{ $user->id }}, '{{ $user->username }}')"
                                                class="text-teal-600 hover:text-teal-800 transition p-1" title="Reset Password">
                                                <i class="fas fa-key text-sm sm:text-lg"></i>
                                            </button>
                                            <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->username }}')"
                                                class="text-red-600 hover:text-red-800 transition p-1" title="Hapus">
                                                <i class="fas fa-trash-alt text-sm sm:text-lg"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-3xl sm:text-4xl mb-2 block"></i>
                                    Tidak ada data user
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODALS -->
    <!-- MODAL TAMBAH USER -->
    <div id="addModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up"
            style="width: 90%; max-width: 360px;">
            <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-plus text-green-500 text-sm"></i>
                    <h3 class="font-semibold text-gray-800 text-sm">Tambah User Baru</h3>
                </div>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('contents.users.store') }}">
                @csrf
                <div class="px-3 py-3 space-y-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" required
                            class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Role</label>
                        <select name="role_id" required
                            class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                @if(in_array($role->role_name, ['admin', 'operator']))
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-1.5 text-xs text-amber-700">
                        <i class="fas fa-info-circle mr-1"></i> Password default: <strong>12345678</strong>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                    <button type="button" onclick="closeAddModal()"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm text-xs">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT USER -->
    <div id="editModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up"
            style="width: 90%; max-width: 360px;">
            <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-edit text-blue-500 text-sm"></i>
                    <h3 class="font-semibold text-gray-800 text-sm">Edit User</h3>
                </div>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="px-3 py-3 space-y-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="edit_username" required
                            class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" required
                            class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Role</label>
                        <select name="role_id" id="edit_role_id" required
                            class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition text-sm">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm text-xs">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL RESET PASSWORD -->
    <div id="resetModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up"
            style="width: 90%; max-width: 360px;">
            <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-key text-teal-500 text-sm"></i>
                    <h3 class="font-semibold text-gray-800 text-sm">Reset Password</h3>
                </div>
                <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="px-3 py-3">
                <p class="text-sm text-gray-600">Yakin ingin reset password user <strong id="reset_username"></strong>?</p>
                <div class="bg-amber-50 rounded-lg p-2 mt-3 text-xs text-amber-700">
                    <i class="fas fa-info-circle mr-1"></i> Password akan direset menjadi: <strong>12345678</strong>
                </div>
            </div>
            <form id="resetForm" method="POST">
                @csrf
                @method('PUT')
                <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                    <button type="button" onclick="closeResetModal()"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition shadow-sm text-xs">Ya,
                        Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TOGGLE STATUS -->
    <div id="toggleModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up"
            style="width: 90%; max-width: 360px;">
            <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-power-off text-amber-500 text-sm"></i>
                    <h3 class="font-semibold text-gray-800 text-sm">Ubah Status User</h3>
                </div>
                <button onclick="closeToggleModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="px-3 py-3">
                <p class="text-sm text-gray-600">Yakin ingin <strong id="toggle_action"></strong> user <strong
                        id="toggle_username"></strong>?</p>
                <p class="text-xs text-gray-500 mt-2" id="toggle_message"></p>
            </div>
            <form id="toggleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                    <button type="button" onclick="closeToggleModal()"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition shadow-sm text-xs">Ya,
                        Ubah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS USER -->
    <div id="deleteModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm mx-auto shadow-xl animate-fade-in-up"
            style="width: 90%; max-width: 360px;">
            <div class="border-b border-gray-100 px-3 py-2.5 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-trash-alt text-red-500 text-sm"></i>
                    <h3 class="font-semibold text-gray-800 text-sm">Hapus User</h3>
                </div>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="px-3 py-3">
                <p class="text-sm text-gray-600">Yakin ingin menghapus user <strong id="delete_username"></strong>?</p>
                <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="border-t border-gray-100 px-3 py-2.5 flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-lg transition text-xs">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-xs">Ya,
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

        body {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        * {
            max-width: 100%;
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
            let url = '{{ route('contents.users.update', ':id') }}';
            url = url.replace(':id', id);
            document.getElementById('editForm').action = url;
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
            let url = '{{ route('contents.users.reset-password', ':id') }}';
            url = url.replace(':id', id);
            document.getElementById('resetForm').action = url;
            document.getElementById('reset_username').innerText = username;
            document.getElementById('resetModal').classList.remove('hidden');
            document.getElementById('resetModal').classList.add('flex');
        }
        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            document.getElementById('resetModal').classList.remove('flex');
        }

        function openToggleModal(id, username, currentStatus) {
            let url = '{{ route('contents.users.toggle-status', ':id') }}';
            url = url.replace(':id', id);
            document.getElementById('toggleForm').action = url;
            let action = currentStatus == 1 ? 'nonaktifkan' : 'aktifkan';
            let messageHtml = currentStatus == 1 ?
                '<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i> User yang <strong>dinonaktifkan</strong> tidak akan bisa login.' :
                '<i class="fas fa-check-circle text-green-500 mr-1"></i> User yang <strong>diaktifkan</strong> akan bisa mengakses halamannya kembali.';
            document.getElementById('toggle_action').innerText = action;
            document.getElementById('toggle_username').innerText = username;
            document.getElementById('toggle_message').innerHTML = messageHtml;
            document.getElementById('toggleModal').classList.remove('hidden');
            document.getElementById('toggleModal').classList.add('flex');
        }
        function closeToggleModal() {
            document.getElementById('toggleModal').classList.add('hidden');
            document.getElementById('toggleModal').classList.remove('flex');
        }

        function openDeleteModal(id, username) {
            let url = '{{ route('contents.users.destroy', ':id') }}';
            url = url.replace(':id', id);
            document.getElementById('deleteForm').action = url;
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