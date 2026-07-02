<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Griya PMI Surakarta')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">

    <style>
        body { font-family: 'Open Sans', sans-serif; }
        #sidebar-wrap { transition: transform 0.25s ease; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay"
         onclick="closeSidebar()"
         class="fixed inset-0 bg-black/40 z-30 hidden"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar wrapper: mobile = fixed + off-screen, desktop = relative + visible --}}
        <div id="sidebar-wrap" class="fixed inset-y-0 left-0 z-40 md:relative md:z-auto flex-shrink-0">
            @include('components.sidebar')
        </div>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            @include('components.navbar')

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    @include('ui.alert', ['type' => 'success', 'message' => session('success')])
                @endif
                @if(session('error'))
                    @include('ui.alert', ['type' => 'error', 'message' => session('error')])
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-4 mb-4">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js">
        const sidebarWrap    = document.getElementById('sidebar-wrap');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        // Desktop: selalu tampil; Mobile: sembunyikan secara default
        function applyInitial() {
            if (window.innerWidth < 768) {
                sidebarWrap.style.transform = 'translateX(-100%)';
            } else {
                sidebarWrap.style.transform = 'translateX(0)';
            }
        }

        function openSidebar() {
            sidebarWrap.style.transform = 'translateX(0)';
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            if (window.innerWidth < 768) {
                sidebarWrap.style.transform = 'translateX(-100%)';
                sidebarOverlay.classList.add('hidden');
            }
        }

        function toggleSidebar() {
            const isOpen = sidebarWrap.style.transform === 'translateX(0px)' ||
                           sidebarWrap.style.transform === 'translateX(0)';
            isOpen ? closeSidebar() : openSidebar();
        }

        applyInitial();
        window.addEventListener('resize', applyInitial);
    </script>

    @stack('scripts')
</body>
</html>
