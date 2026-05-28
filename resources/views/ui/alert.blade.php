@php
    $styles = match($type ?? 'success') {
        'success' => ['bg-green-50 border-green-200 text-green-800', 'fa-circle-check text-green-500'],
        'error'   => ['bg-red-50 border-red-200 text-red-800', 'fa-circle-xmark text-red-500'],
        'warning' => ['bg-yellow-50 border-yellow-200 text-yellow-800', 'fa-triangle-exclamation text-yellow-500'],
        default   => ['bg-blue-50 border-blue-200 text-blue-800', 'fa-circle-info text-blue-500'],
    };
@endphp

<div class="flex items-center gap-3 px-4 py-3 rounded-lg border {{ $styles[0] }} mb-4">
    <i class="fa-solid {{ $styles[1] }}"></i>
    <span class="text-sm">{{ $message }}</span>
</div>
