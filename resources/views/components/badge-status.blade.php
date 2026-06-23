@php
    $config = match($status) {
        'Aman'              => ['bg-green-100 text-green-700',  'fa-circle-check',         'Aman'],
        'Mendesak'          => ['bg-yellow-100 text-yellow-700','fa-triangle-exclamation',  'Mendesak'],
        'Sangat Mendesak'   => ['bg-red-100 text-red-700',      'fa-circle-xmark',          'Sangat Mendesak'],
        'Aktif'             => ['bg-green-100 text-green-700',  'fa-circle-check',         'Aktif'],
        'Selesai Pembinaan' => ['bg-gray-100 text-gray-600',    'fa-circle-check',         'Selesai Pembinaan'],
        'Meninggal'         => ['bg-red-100 text-red-700',      'fa-circle-xmark',         'Meninggal'],
        'Kabur'            => ['bg-orange-100 text-orange-700','fa-circle-question',       'Kabur'],
        'PROSES'            => ['bg-yellow-100 text-yellow-700','fa-clock',                 'PROSES'],
        'DISETUJUI'         => ['bg-green-100 text-green-700',  'fa-circle-check',         'DISETUJUI'],
        'DITOLAK'           => ['bg-red-100 text-red-700',      'fa-circle-xmark',          'DITOLAK'],
        'MENUNGGU'          => ['bg-yellow-100 text-yellow-700','fa-clock',                 'MENUNGGU'],
        'DIVERIFIKASI'      => ['bg-green-100 text-green-700',  'fa-circle-check',         'DIVERIFIKASI'],
        'DIVALIDASI'        => ['bg-green-100 text-green-700',  'fa-circle-check',         'DIVALIDASI'],
        'DIBATALKAN'        => ['bg-gray-100 text-gray-600',    'fa-ban',                   'DIBATALKAN'],
        'PUBLISHED'         => ['bg-green-100 text-green-700',  'fa-circle-check',         'PUBLISHED'],
        'DRAFT'             => ['bg-gray-100 text-gray-600',    'fa-pencil',                'DRAFT'],
        // Donasi Statuses
        'Tunggu Verifikasi' => ['bg-yellow-100 text-yellow-700','fa-clock',                 'Tunggu Verifikasi'],
        'Donasi Ditolak'    => ['bg-red-100 text-red-700',      'fa-circle-xmark',          'Donasi Ditolak'],
        'Menunggu Pengiriman'=>['bg-blue-100 text-blue-700',    'fa-truck',                 'Menunggu Pengiriman'],
        'Menunggu Donasi Dijemput Petugas' => ['bg-blue-100 text-blue-700', 'fa-truck-ramp-box', 'Menunggu Dijemput Petugas'],
        'Selesai'           => ['bg-green-100 text-green-700',  'fa-circle-check',          'Selesai'],
        'Disetujui'         => ['bg-green-100 text-green-700',  'fa-circle-check',         'Disetujui'],
        'Ditolak'           => ['bg-red-100 text-red-700',      'fa-circle-xmark',          'Ditolak'],
        default             => ['bg-gray-100 text-gray-600',    'fa-circle',                $status],
    };
@endphp

<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $config[0] }}">
    <i class="fa-solid {{ $config[1] }} text-xs"></i>
    {{ $config[2] }}
</span>
