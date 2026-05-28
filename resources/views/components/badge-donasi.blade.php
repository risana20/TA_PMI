@php
    $cfg = match(strtolower($status ?? '')) {
        'diverifikasi', 'verified', 'telah diserahkan'
            => ['bg-green-100 text-green-700',   'fa-circle-check',         ucfirst($status)],
        'proses validasi', 'menunggu', 'pending', 'proses penerimaan'
            => ['bg-yellow-100 text-yellow-700', 'fa-clock',                ucfirst($status)],
        'ditolak', 'rejected'
            => ['bg-red-100 text-red-700',       'fa-circle-xmark',         'Ditolak'],
        default
            => ['bg-gray-100 text-gray-600',     'fa-circle',               ucfirst($status ?? '-')],
    };
@endphp

<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg[0] }}">
    <i class="fa-solid {{ $cfg[1] }} text-xs"></i>
    {{ $cfg[2] }}
</span>
