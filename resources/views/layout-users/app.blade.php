<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FlowLab</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @yield('style')
</head>
<body class="bg-slate-50 flex flex-col min-h-screen  m-0 p-0">
    <header class="w-full top-0 z-10">
        @include('layout-users.navbar')
    </header>

    <main class="flex-1 relative z-1">
        @yield('content')
    </main>

    <footer class="bg-sky-500 text-white py-4 relative z-10">
        @include('layout-users.footer')
    </footer>

    @yield('script')
</body>
</html>
