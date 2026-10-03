@props([
    'icon',
    'value',
    'title',
    'href' => 'javascript:void(0);',
    'footerLabel' => null,
    'footerValue' => null,
    'footerPercent' => null,
    'progress' => 'primary',
])

<div class="card stretch stretch-full" {{ $attributes }}>
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-4">
            <div class="d-flex gap-4 align-items-center">
                <div class="avatar-text avatar-lg bg-gray-200">
                    <i class="feather-{{ $icon }}"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ $value }}</div>
                    <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $title }}</h3>
                </div>
            </div>
            <a href="{{ $href }}">
                <i class="feather-more-vertical"></i>
            </a>
        </div>
        @if ($footerLabel)
            <div class="pt-4">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ $href }}" class="fs-12 fw-medium text-muted text-truncate-1-line">{{ $footerLabel }}</a>
                    <div class="w-100 text-end">
                        <span class="fs-12 text-dark">{{ $footerValue }}</span>
                        <span class="fs-11 text-muted">({{ $footerPercent }})</span>
                    </div>
                </div>
                <div class="progress mt-2 ht-3">
                    <div class="progress-bar bg-{{ $progress }}" role="progressbar" style="width: {{ $footerPercent }}"></div>
                </div>
            </div>
        @endif
    </div>
</div>
