@props([
    'variant' => 'soft',
    'color' => 'primary',
    'header' => false,
])

@php
    $classes = match ($variant) {
        'solid' => 'badge bg-' . $color,
        'gray' => 'badge small bg-gray-200 rounded ms-1 text-dark',
        'border' => 'badge border rounded text-dark',
        default => 'badge bg-soft-' . $color . ' text-' . $color,
    };

    if ($header) {
        $classes .= ' nxl-h-badge';
    }
@endphp
<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
