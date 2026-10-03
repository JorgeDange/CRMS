@props(['title', 'breadcrumb' => null])

@php
    $trail = $breadcrumb ?: [$title];
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
