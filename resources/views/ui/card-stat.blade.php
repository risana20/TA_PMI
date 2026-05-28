<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            @isset($sub)
                <p class="text-xs text-gray-500 mt-1">{{ $sub }}</p>
            @endisset
        </div>
        <div class="w-12 h-12 rounded-xl {{ $iconBg ?? 'bg-red-100' }} flex items-center justify-center">
            <i class="fa-solid {{ $icon }} {{ $iconColor ?? 'text-red-600' }} text-lg"></i>
        </div>
    </div>
</div>
