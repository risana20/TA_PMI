@php
    $cfg = match(strtoupper($status ?? '')) {
        'DISETUJUI'
            => ['bg-green-100 text-green-700',   'fa-circle-check',         'Disetujui'],
        'PROSES', 'MENUNGGU'
            => ['bg-yellow-100 text-yellow-700', 'fa-clock',                'Menunggu'],
        'DITOLAK'
            => ['bg-red-100 text-red-700',       'fa-circle-xmark',         'Ditolak'],
        default
            => ['bg-gray-100 text-gray-600',     'fa-circle',               ucfirst(strtolower($status ?? '-'))],
    };
@endphp

<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg[0] }}">
    <i class="fa-solid {{ $cfg[1] }} text-xs"></i>
    {{ $cfg[2] }}
</span>
