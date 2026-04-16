<aside id="sidebar" class="w-64 p-6 flex flex-col justify-between min-h-screen"
    style="background: linear-gradient(180deg, #e8d5a8 0%, #ddc89a 100%); box-shadow: 4px 0 20px rgba(180,140,60,0.10);">

    <div>
        <!-- Tombol close (mobile) -->
        <div class="flex justify-end mb-2 md:hidden">
            <button id="closeSidebarBtn" class="text-[#5a4a1e] text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex justify-center mb-8 mt-4">
            <img src="{{ asset('images/Logo LF.png') }}" alt="Logo Perusahaan" class="w-64 h-auto object-contain"
                onerror="this.src='{{ asset('images/default-logo.png') }}'">
        </div>

        <nav class="space-y-2">
            {{-- DASHBOARD --}}
            <a href="{{ route('contents.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.dashboard') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-tachometer-alt w-5"></i> Dasbor
            </a>

            {{-- USER MANAGEMENT --}}
            <a href="{{ route('contents.users') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.users') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-users w-5"></i> Manajemen User
            </a>

            {{-- CATEGORY --}}
            <a href="{{ route('contents.categories') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.categories') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-tags w-5"></i> Kategori
            </a>

            {{-- Manajemen Produk (Sub-menu) --}}
            <div class="relative">
                <button onclick="toggleProductSubmenu()"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('contents.productmanage*') || request()->routeIs('contents.stockmanage*') || request()->routeIs('contents.mutasi*') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-box w-5"></i> Manajemen Produk
                    </div>
                    <i id="productSubmenuIcon"
                        class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                </button>
                <div id="productSubmenu" class="mt-1 ml-6 space-y-1 hidden">
                    <a href="{{ route('contents.productmanage') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('contents.productmanage') ? 'text-[#c9973a] font-semibold' : 'text-[#5a4a1e] hover:text-[#c9973a]' }} text-sm transition">
                        <i class="fas fa-cube w-4"></i> Produk
                    </a>
                    <a href="{{ route('contents.stockmanage') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('contents.stockmanage') ? 'text-[#c9973a] font-semibold' : 'text-[#5a4a1e] hover:text-[#c9973a]' }} text-sm transition">
                        <i class="fas fa-warehouse w-4"></i> Stok
                    </a>
                    <a href="{{ route('contents.mutasi') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('contents.mutasi') ? 'text-[#c9973a] font-semibold' : 'text-[#5a4a1e] hover:text-[#c9973a]' }} text-sm transition">
                        <i class="fas fa-exchange-alt w-4"></i> Mutasi
                    </a>
                </div>
            </div>

            {{-- REPORT --}}
            <a href="{{ route('contents.reports') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.reports') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-chart-line w-5"></i> Laporan
            </a>

            {{-- TRASH KHUSUS SUPER ADMIN --}}
            <a href="{{ route('contents.productmanage.trash') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('contents.productmanage.trash') ? 'bg-[#c9973a] text-white' : 'bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white' }} font-medium text-sm transition-all duration-200 shadow">
                <i class="fas fa-trash-alt w-5"></i> Sampah
            </a>
        </nav>
    </div>

    {{-- SETTING (Manage Account + Logout) --}}
    <div class="mt-6">
        <div class="relative">
            <button onclick="toggleSettingMenu()"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-white text-[#5a4a1e] hover:bg-[#c9973a] hover:text-white font-medium text-sm transition-all duration-200 shadow">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cog w-5"></i> Pengaturan
                </div>
                <i id="settingMenuIcon" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
            </button>
            <div id="settingMenu" class="mt-1 space-y-1 hidden">
                <a href="{{ route('manage-account') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg text-[#5a4a1e] hover:text-[#c9973a] text-sm transition">
                    <i class="fas fa-user-cog w-4"></i> Kelola Akun
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 text-sm transition">
                        <i class="fas fa-sign-out-alt w-4"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<script>
    function toggleProductSubmenu() {
        const submenu = document.getElementById('productSubmenu');
        const icon = document.getElementById('productSubmenuIcon');
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            submenu.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function toggleSettingMenu() {
        const menu = document.getElementById('settingMenu');
        const icon = document.getElementById('settingMenuIcon');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const currentRoute = window.location.href;
        if (currentRoute.includes('/productmanage') || currentRoute.includes('/stockmanage') || currentRoute
            .includes('/mutasi')) {
            const submenu = document.getElementById('productSubmenu');
            const icon = document.getElementById('productSubmenuIcon');
            if (submenu && submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    });
</script>
