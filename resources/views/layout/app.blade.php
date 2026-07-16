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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">

    <style>
        body { font-family: 'Open Sans', sans-serif; }
        #sidebar-wrap {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Prevent body scroll when sidebar overlay is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }
        @media (min-width: 768px) {
            body.sidebar-open {
                overflow: auto;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- Overlay — klik untuk tutup sidebar di mobile --}}
    <div id="sidebar-overlay"
         onclick="closeSidebar()"
         class="fixed inset-0 bg-black/40 z-30 hidden transition-opacity duration-300"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar wrapper --}}
        {{-- Mobile: posisi fixed off-screen kiri, slide masuk saat dibuka --}}
        {{-- Desktop: bagian dari flex row, tidak tertutup overlay --}}
        <div id="sidebar-wrap"
             class="fixed inset-y-0 left-0 z-40 w-64
                    md:static md:z-auto md:flex-shrink-0">

            {{-- Close button (mobile only) --}}
            <button id="sidebar-close-btn"
                    onclick="closeSidebar()"
                    class="md:hidden absolute top-3 right-3 z-50 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 text-gray-500 hover:text-red-600 transition-colors"
                    aria-label="Tutup sidebar">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            @include('components.sidebar')
        </div>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            @include('components.navbar')

            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
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

    {{-- Tom Select (src saja, tidak boleh ada inline code di sini) --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

    {{-- Sidebar toggle logic --}}
    <script>
        const sidebarWrap    = document.getElementById('sidebar-wrap');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function isMobile() {
            return window.innerWidth < 768;
        }

        // Terapkan posisi awal sidebar berdasarkan lebar layar
        function applyInitial() {
            if (isMobile()) {
                // Mobile: sembunyikan sidebar (geser ke kiri)
                sidebarWrap.style.transform = 'translateX(-100%)';
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('sidebar-open');
            } else {
                // Desktop: tampilkan sidebar
                sidebarWrap.style.transform = 'translateX(0)';
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('sidebar-open');
            }
        }

        function openSidebar() {
            sidebarWrap.style.transform = 'translateX(0)';
            if (isMobile()) {
                sidebarOverlay.classList.remove('hidden');
                document.body.classList.add('sidebar-open');
            }
        }

        function closeSidebar() {
            if (isMobile()) {
                sidebarWrap.style.transform = 'translateX(-100%)';
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('sidebar-open');
            }
        }

        function toggleSidebar() {
            const currentTransform = sidebarWrap.style.transform;
            const isHidden = currentTransform === 'translateX(-100%)';
            if (isHidden) {
                openSidebar();
            } else {
                closeSidebar();
            }
        }

        // Tutup sidebar saat user klik salah satu menu di mobile
        document.querySelectorAll('#sidebar-wrap a').forEach(function(link) {
            link.addEventListener('click', function() {
                if (isMobile()) {
                    closeSidebar();
                }
            });
        });

        // Tutup sidebar dengan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMobile()) {
                closeSidebar();
            }
        });

        applyInitial();
        window.addEventListener('resize', applyInitial);
    </script>

    @stack('scripts')
</body>
</html>
