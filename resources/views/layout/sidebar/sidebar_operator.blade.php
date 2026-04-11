<aside class="w-64 p-6 flex flex-col justify-between min-h-screen"
    style="background: linear-gradient(180deg, #e8d5a8 0%, #ddc89a 100%); box-shadow: 4px 0 20px rgba(180,140,60,0.10);">

    <div>
        <div class="flex justify-center mb-10 mt-4">
            <img src="{{ asset('images/Logo LF.png') }}" alt="Logo Perusahaan" class="w-64 h-auto object-contain"
                onerror="this.src='{{ asset('images/default-logo.png') }}'">
        </div>

        <nav class="space-y-2">
            <a href="{{ route('contents.operator.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.operator.dashboard') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>

            <a href="{{ route('manage-account') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.manage-account') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-user-cog w-5"></i> Manage Account Operator
            </a>

            <a href="{{ route('contents.operator.productmanage') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.operator.productmanage') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-box w-5"></i> Product Management
            </a>

            <a href="{{ route('contents.operator.stock') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.operator.stock') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-warehouse w-5"></i> Stock Management
            </a>

            <a href="{{ route('contents.reports') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.reports') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-chart-line w-5"></i> Report
            </a>

            <!-- TAMBAHKAN MENU TRASH DI SINI -->
            <a href="{{ route('contents.operator.productmanage.trash') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.operator.productmanage.trash') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-trash-alt w-5"></i> Trash
            </a>
        </nav>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white text-[#5a4a1e] font-medium text-sm transition-all duration-200 shadow hover:bg-[#c9973a] hover:text-white">
            <i class="fas fa-sign-out-alt w-5"></i>
            Logout
        </button>
    </form>
</aside>
