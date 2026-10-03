@props(['variant' => 'success'])

<span {{ $attributes->merge(['class' => 'badge nxl-h-badge bg-' . $variant]) }}>{{ $slot }}</span>
