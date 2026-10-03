@props([
    'variant' => 'primary',
    'size' => null,
    'href' => null,
    'icon' => null,
    'type' => 'button',
    'iconOnly' => false,
])

@php
    $tag = $href ? 'a' : 'button';
    $classes = trim('btn btn-' . $variant . ($size ? ' btn-' . $size : '') . ($iconOnly ? ' btn-icon' : ''));
@endphp
<{{ $tag }}@if(! $href) type="{{ $type }}"@endif @if($href) href="{{ $href }}"@endif {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <i class="feather-{{ $icon }}{{ trim((string) $slot) !== '' && ! $iconOnly ? ' me-2' : '' }}"></i>
    @endif
    {{ $slot }}
</{{ $tag }}>
