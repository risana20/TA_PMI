<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Griya PMI Surakarta')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/pmi-favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Open Sans', sans-serif; } </style>
    @stack('styles')
</head>
<body class="bg-white text-gray-800">
    @include('components.navbar-public')
    <main>
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 pt-4">
                @include('ui.alert', ['type' => 'success', 'message' => session('success')])
            </div>
        @endif
        @yield('content')
    </main>
    @include('sections.footer')
    @stack('scripts')
</body>
</html>
