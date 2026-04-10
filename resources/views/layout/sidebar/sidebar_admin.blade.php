    <div class="w-64 bg-[#cbb892] p-6 shadow-lg flex flex-col justify-between">

        <!-- TOP (LOGO + MENU) -->
        <div>

            <!-- Logo -->
            <div class="flex items-center gap-2 mb-8 border-b border-[#a88454] pb-4">
                <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-72">
            </div>

            <!-- Menu -->
            <div class="space-y-2">

                <a href="#"
                    class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('contents.dashboard') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('contents.manage-admin') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                    <i class="fas fa-user-cog w-5"></i> Manage Account Admin
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('contents.users') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                    <i class="fas fa-users w-5"></i> User Management
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('contents.categories') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                    <i class="fas fa-tags w-5"></i> Category
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 rounded-lg
                {{ request()->routeIs('contents.reports') ? 'bg-white/90 font-semibold shadow-sm' : 'text-[#2c2b26] hover:bg-white/70' }}">
                    <i class="fas fa-chart-line w-5"></i> Report
                </a>

            </div>
        </div>

        <!-- BOTTOM (LOGOUT) -->
        <form action="/logout" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 p-3 rounded-lg text-red-700 hover:bg-red-100 transition">
                <i class="fas fa-sign-out-alt w-5"></i>
                Logout
            </button>
        </form>

    </div>