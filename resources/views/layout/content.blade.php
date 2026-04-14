<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Luxury Furniture - @yield('title')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-[#f4efe3] font-sans">

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

        <div class="flex-1 p-6">
            @yield('content')
        </div>
    </div>

    @yield('scripts')

</body>

</html>