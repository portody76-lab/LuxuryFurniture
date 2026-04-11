@extends('layout.content')

@section('title', 'Manage Account Admin')

@section('content')
<div class="min-h-[calc(100vh-3rem)] flex items-center justify-center">
    <div class="max-w-4xl w-full mx-auto">
        
        <!-- Header Section dengan Icon Profile -->
        <div class="mb-8 text-center">
            <!-- Avatar / Icon Profile -->
            <div class="mb-4">
                <div class="w-20 h-20 bg-linear-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center mx-auto shadow-lg">
                    <i class="fas fa-user-circle text-white text-5xl"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Manage Account Admin</h1>
            <p class="text-gray-500 mt-1">Kelola data akun admin Anda sendiri</p>
        </div>

        <!-- Info Card - Elegant -->
        <div class="bg-linear-to-r from-amber-50 to-amber-100/30 rounded-2xl p-6 mb-8 border border-amber-200/50 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-user-circle text-amber-600 text-xl"></i>
                <h3 class="text-sm font-semibold text-amber-800 uppercase tracking-wide">Informasi Akun</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Username</p>
                    <p class="text-gray-800 font-medium">{{ auth()->user()->username }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Role</p>
                    <p class="text-gray-800 font-medium">{{ auth()->user()->role->role_name ?? 'Admin' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                    <p class="text-green-600 font-medium flex items-center gap-1">
                        <i class="fas fa-circle text-[8px] text-green-500"></i>
                        {{ auth()->user()->status ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if($errors->any() && !$errors->has('username') && !$errors->has('current_password') && !$errors->has('new_password'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Change Username Card -->
            <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-200 transition">
                        <i class="fas fa-user-pen text-amber-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-1">Change Username</h3>
                    <p class="text-gray-400 text-sm mb-5">Perbarui username akun Anda</p>
                    <button onclick="openUsernameModal()" 
                        class="w-full bg-gray-50 hover:bg-amber-50 text-gray-700 hover:text-amber-700 font-medium py-2.5 rounded-xl transition-all duration-200 border border-gray-200 hover:border-amber-200">
                        Edit Username
                    </button>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-200 transition">
                        <i class="fas fa-key text-amber-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-1">Change Password</h3>
                    <p class="text-gray-400 text-sm mb-5">Perbarui password akun Anda</p>
                    <button onclick="openPasswordModal()" 
                        class="w-full bg-gray-50 hover:bg-amber-50 text-gray-700 hover:text-amber-700 font-medium py-2.5 rounded-xl transition-all duration-200 border border-gray-200 hover:border-amber-200">
                        Edit Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT USERNAME -->
<div id="usernameModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
        <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-user-pen text-amber-500"></i>
                <h3 class="text-xl font-bold text-gray-800">Edit Username</h3>
            </div>
            <button onclick="closeUsernameModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('contents.admin.manage-admin.update-username') }}">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" 
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition"
                    placeholder="Masukkan username baru">
                <div id="usernameError" class="text-red-500 text-sm mt-2 hidden"></div>
            </div>
            
            <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                <button type="button" onclick="closeUsernameModal()" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl transition shadow-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PASSWORD -->
<div id="passwordModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl animate-fade-in-up">
        <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-key text-amber-500"></i>
                <h3 class="text-xl font-bold text-gray-800">Edit Password</h3>
            </div>
            <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('contents.admin.manage-admin.update-password') }}">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Lama</label>
                    <input type="password" name="current_password" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition"
                        placeholder="Masukkan password lama">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="new_password" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition"
                        placeholder="Masukkan password baru">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition"
                        placeholder="Ulangi password baru">
                </div>
                <div id="passwordError" class="text-red-500 text-sm hidden"></div>
            </div>
            
            <div class="border-t border-gray-100 px-6 py-5 flex justify-end gap-3">
                <button type="button" onclick="closePasswordModal()" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl transition shadow-sm">Update</button>
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
    function openUsernameModal() {
        document.getElementById('usernameModal').classList.remove('hidden');
        document.getElementById('usernameModal').classList.add('flex');
        document.getElementById('usernameError').classList.add('hidden');
    }
    
    function closeUsernameModal() {
        document.getElementById('usernameModal').classList.add('hidden');
        document.getElementById('usernameModal').classList.remove('flex');
    }
    
    function openPasswordModal() {
        document.getElementById('passwordModal').classList.remove('hidden');
        document.getElementById('passwordModal').classList.add('flex');
        document.getElementById('passwordError').classList.add('hidden');
    }
    
    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
        document.getElementById('passwordModal').classList.remove('flex');
    }
    
    @if($errors->any())
        @if($errors->has('username'))
            openUsernameModal();
            document.getElementById('usernameError').innerHTML = '{{ $errors->first('username') }}';
            document.getElementById('usernameError').classList.remove('hidden');
        @endif
        
        @if($errors->has('current_password') || $errors->has('new_password'))
            openPasswordModal();
            var errorMsg = '';
            @if($errors->has('current_password'))
                errorMsg += '{{ $errors->first('current_password') }}';
            @endif
            @if($errors->has('new_password'))
                errorMsg += '{{ $errors->first('new_password') }}';
            @endif
            document.getElementById('passwordError').innerHTML = errorMsg;
            document.getElementById('passwordError').classList.remove('hidden');
        @endif
    @endif
    
    @if(session('success'))
        closeUsernameModal();
        closePasswordModal();
    @endif
    
    window.onclick = function(event) {
        const usernameModal = document.getElementById('usernameModal');
        const passwordModal = document.getElementById('passwordModal');
        if (event.target === usernameModal) closeUsernameModal();
        if (event.target === passwordModal) closePasswordModal();
    }
</script>
@endsection