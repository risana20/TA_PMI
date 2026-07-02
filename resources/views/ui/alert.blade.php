@php
    $alertId  = 'alert-' . uniqid();
    $config   = match($type ?? 'info') {
        'success' => [
            'bar'      => 'bg-green-500',
            'bg'       => 'bg-white',
            'border'   => 'border-l-4 border-green-500',
            'icon_bg'  => 'bg-green-100',
            'icon'     => 'fa-circle-check',
            'icon_col' => 'text-green-600',
            'title'    => 'Berhasil!',
            'text'     => 'text-gray-800',
            'sub'      => 'text-gray-500',
            'close'    => 'text-gray-400 hover:text-gray-600',
        ],
        'error' => [
            'bar'      => 'bg-[#CC0001]',
            'bg'       => 'bg-white',
            'border'   => 'border-l-4 border-[#CC0001]',
            'icon_bg'  => 'bg-red-100',
            'icon'     => 'fa-circle-exclamation',
            'icon_col' => 'text-[#CC0001]',
            'title'    => 'Terjadi Kesalahan',
            'text'     => 'text-gray-800',
            'sub'      => 'text-gray-500',
            'close'    => 'text-gray-400 hover:text-[#CC0001]',
        ],
        'warning' => [
            'bar'      => 'bg-amber-500',
            'bg'       => 'bg-white',
            'border'   => 'border-l-4 border-amber-500',
            'icon_bg'  => 'bg-amber-100',
            'icon'     => 'fa-triangle-exclamation',
            'icon_col' => 'text-amber-600',
            'title'    => 'Perhatian',
            'text'     => 'text-gray-800',
            'sub'      => 'text-gray-500',
            'close'    => 'text-gray-400 hover:text-amber-600',
        ],
        default => [
            'bar'      => 'bg-blue-500',
            'bg'       => 'bg-white',
            'border'   => 'border-l-4 border-blue-500',
            'icon_bg'  => 'bg-blue-100',
            'icon'     => 'fa-circle-info',
            'icon_col' => 'text-blue-600',
            'title'    => 'Informasi',
            'text'     => 'text-gray-800',
            'sub'      => 'text-gray-500',
            'close'    => 'text-gray-400 hover:text-blue-600',
        ],
    };
@endphp

<style>
    @keyframes slideInAlert {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shrinkBar {
        from { width: 100%; }
        to   { width: 0%; }
    }
    .pmi-alert-enter {
        animation: slideInAlert 0.35s cubic-bezier(.21,1.02,.73,1) forwards;
    }
    .pmi-alert-leave {
        animation: slideInAlert 0.25s cubic-bezier(.21,1.02,.73,1) reverse forwards;
    }
</style>

<div id="{{ $alertId }}"
     class="pmi-alert-enter relative {{ $config['bg'] }} {{ $config['border'] }} rounded-xl shadow-md mb-4 overflow-hidden"
     role="alert">

    {{-- Progress bar auto-dismiss --}}
    <div class="absolute top-0 left-0 h-1 {{ $config['bar'] }} rounded-tl-xl"
         style="animation: shrinkBar 6s linear forwards;"
         id="{{ $alertId }}-bar"></div>

    <div class="flex items-start gap-4 px-5 py-4">
        {{-- Icon --}}
        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $config['icon_bg'] }} flex items-center justify-center mt-0.5">
            <i class="fa-solid {{ $config['icon'] }} {{ $config['icon_col'] }} text-lg"></i>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm {{ $config['text'] }}">{{ $config['title'] }}</p>
            <p class="text-sm {{ $config['sub'] }} mt-0.5 leading-relaxed">{{ $message }}</p>
        </div>

        {{-- Close button --}}
        <button onclick="dismissAlert('{{ $alertId }}')"
                class="flex-shrink-0 {{ $config['close'] }} transition-colors duration-150 mt-0.5"
                aria-label="Tutup notifikasi">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>
</div>

<script>
    (function() {
        var id  = '{{ $alertId }}';
        var el  = document.getElementById(id);
        var bar = document.getElementById(id + '-bar');

        // Auto-dismiss after 6 seconds
        setTimeout(function() { dismissAlert(id); }, 6000);
    })();

    function dismissAlert(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('pmi-alert-enter');
        el.classList.add('pmi-alert-leave');
        el.addEventListener('animationend', function() { el.remove(); }, { once: true });
    }
</script>
