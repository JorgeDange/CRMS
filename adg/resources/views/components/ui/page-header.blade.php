@props(['title', 'breadcrumb' => null])

@php
    // breadcrumb: lista de itens APOS "Início" (array ou string única).
    // Default: [title] — ex. Início / Oportunidades.
    if (is_string($breadcrumb)) {
        $breadcrumb = trim(strip_tags($breadcrumb)) !== '' ? [trim(strip_tags($breadcrumb))] : null;
    }
    $trail = is_array($breadcrumb) && $breadcrumb !== [] ? array_values($breadcrumb) : [$title];
@endphp
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $title }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate>Início</a></li>
            @foreach ($trail as $item)
                <li class="breadcrumb-item">{{ $item }}</li>
            @endforeach
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                {{ $right ?? '' }}
            </div>
        </div>
    </div>
</div>
