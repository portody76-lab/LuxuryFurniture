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
        /* Sidebar fixed untuk desktop */
        @media (min-width: 768px) {
            #sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                overflow-y: auto;
                z-index: 40;
            }
            
            #mainContent {
                margin-left: 16rem; /* 64 * 0.25rem = 16rem */
            }
        }
        
        /* Mobile styles */
        @media (max-width: 767px) {
            #sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 40;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            #sidebar.sidebar-open {
                transform: translateX(0);
            }
            
            #mainContent {
                margin-left: 0;
            }
        }
        
        /* Scrollbar styling */
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        #sidebar::-webkit-scrollbar-track {
            background: #ddc89a;
        }
        
        #sidebar::-webkit-scrollbar-thumb {
            background: #c9973a;
            border-radius: 4px;
        }
        
        html, body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
        }
        
        * {
            max-width: 100%;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideOutRight {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100%); }
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }
        
        .animate-slide-out-right {
            animation: slideOutRight 0.3s ease-in forwards;
        }
        
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f3e4c3;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c9973a;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #b07e28;
        }
    </style>
</head>

<body class="bg-[#f4efe3] font-sans overflow-x-hidden">

    <!-- Header Mobile -->
    <div class="md:hidden flex items-center justify-between bg-[#e8d5a8] px-4 py-3 shadow sticky top-0 z-30">
        <button id="openSidebarBtn" class="focus:outline-none">
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

        <div id="mainContent" class="flex-1 p-3 sm:p-4 md:p-6 overflow-x-hidden w-full max-w-full">
            @yield('content')
        </div>
    </div>

    <!-- ALERT NOTIFIKASI GLOBAL -->
    <div id="alert-container" class="fixed top-4 right-4 z-50 space-y-2 w-80 max-w-[calc(100%-2rem)]">
        @if(session('success'))
            <div class="alert alert-success bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-lg shadow-lg flex items-start gap-2 animate-slide-in-right">
                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                <div class="flex-1 text-sm">{{ session('success') }}</div>
                <button onclick="closeAlert(this)" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-lg shadow-lg flex items-start gap-2 animate-slide-in-right">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                <div class="flex-1 text-sm">{{ session('error') }}</div>
                <button onclick="closeAlert(this)" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 rounded-lg shadow-lg flex items-start gap-2 animate-slide-in-right">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                <div class="flex-1 text-sm">{{ session('warning') }}</div>
                <button onclick="closeAlert(this)" class="text-yellow-500 hover:text-yellow-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-3 rounded-lg shadow-lg flex items-start gap-2 animate-slide-in-right">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div class="flex-1 text-sm">{{ session('info') }}</div>
                <button onclick="closeAlert(this)" class="text-blue-500 hover:text-blue-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-lg shadow-lg flex items-start gap-2 animate-slide-in-right">
                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                <div class="flex-1 text-sm">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="closeAlert(this)" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    </div>

    @yield('scripts')

    <script>
        function closeAlert(btn) {
            const alert = btn.closest('.alert');
            if (alert) {
                alert.classList.remove('animate-slide-in-right');
                alert.classList.add('animate-slide-out-right');
                setTimeout(() => {
                    if (alert.parentElement) alert.remove();
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');

            function openSidebar() {
                if (sidebar) {
                    sidebar.classList.add('sidebar-open');
                }
                if (overlay) overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('sidebar-open');
                }
                if (overlay) overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    if (sidebar) {
                        sidebar.classList.remove('sidebar-open');
                    }
                    if (overlay) overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });

            const alerts = document.querySelectorAll('#alert-container .alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentElement) {
                        alert.classList.remove('animate-slide-in-right');
                        alert.classList.add('animate-slide-out-right');
                        setTimeout(() => {
                            if (alert.parentElement) alert.remove();
                        }, 300);
                    }
                }, 5000);
            });
        });
    </script>
</body>

</html>