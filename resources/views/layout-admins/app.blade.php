<!DOCTYPE html>
<html lang="en">
<head>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @yield('csrf-token')

        <title>FlowMatic</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="{{asset("select2/css/select2-bootstrap4.css")}}"/>
        <link rel="stylesheet" href="{{asset("select2/css/select2.min.css")}}"/>

        @yield('style')
    </head>
</head>
<body class="bg-slate-50 font-fira" @yield('onload')>
    @include('sweetalert2::index')
    <div class="flex">
        <div class="w-[22%]">
            @include('layout-admins.sidebar')
        </div>
        
        <div class="w-[78%] ml-5">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
    <script src="{{asset("select2/js/select2.min.js")}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
    <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
        mermaid.initialize({ startOnLoad: true });
    </script>
    @yield('script')
</body>
</html>