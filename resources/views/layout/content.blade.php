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
        @include('layout.sidebar.sidebar_admin')

        <div class="flex-1 p-6">
            @yield('content')
        </div>
    </div>

    </div>
    @yield('scripts')

</body>

</html>
