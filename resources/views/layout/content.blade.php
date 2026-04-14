<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Luxury Furniture - @yield('title')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        #sidebar {
            transition: transform 0.3s ease !important;
        }

        @media (max-width: 767px) {
            #sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 16rem !important;
                height: 100% !important;
                z-index: 40 !important;
                transform: translateX(-100%) !important;
            }

            #sidebar.sidebar-open {
                transform: translateX(0) !important;
            }
        }

        /* GLOBAL FIX - cegah overflow horizontal */
        html, body {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        * {
            max-width: 100%;
        }
    </style>
</head>

<body class="bg-[#f4efe3] font-sans overflow-x-hidden">

    <!-- Header Mobile -->
    <div class="md:hidden flex items-center justify-between bg-[#e8d5a8] px-4 py-3 shadow sticky top-0 z-30">
        <button id="openSidebarBtn">
            <i class="fas fa-bars text-[#5a4a1e] text-xl"></i>
        </button>
        <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="h-8 object-contain">
        <div class="w-6"></div>
    </div>

    <!-- Overlay Mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

    <div class="flex min-h-screen">
        @auth
            @if(auth()->user()->role->role_name === 'super_admin')
                @include('layout.sidebar.sidebar_super_admin')
            @elseif(auth()->user()->role->role_name === 'admin')
                @include('layout.sidebar.sidebar_admin')
            @elseif(auth()->user()->role->role_name === 'operator')
                @include('layout.sidebar.sidebar_operator')
            @endif
        @endauth

        {{-- PERBAIKAN UTAMA: padding responsif + overflow-x-hidden --}}
        <div id="mainContent" class="flex-1 p-3 sm:p-4 md:p-6 overflow-x-hidden w-full max-w-full">
            @yield('content')
        </div>
    </div>

    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');

            function openSidebar() {
                if (sidebar) {
                    sidebar.classList.add('sidebar-open');
                    sidebar.classList.remove('sidebar-hidden');
                }
                if (overlay) overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('sidebar-open');
                    sidebar.classList.add('sidebar-hidden');
                }
                if (overlay) overlay.classList.add('hidden');
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    if (sidebar) {
                        sidebar.classList.remove('sidebar-hidden', 'sidebar-open');
                    }
                    if (overlay) overlay.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>