@props([
    'id',
    'title' => '',
    'size' => null,
    'centered' => false,
    'scrollable' => false,
])

@php
    $dialogClasses = trim('modal-dialog'
        . ($size ? ' modal-dialog-' . $size : '')
        . ($centered ? ' modal-dialog-centered' : '')
        . ($scrollable ? ' modal-dialog-scrollable' : ''));
@endphp
<div class="modal fade" id="{{ $id }}" tabindex="-1" data-bs-keyboard="false" role="dialog">
    <div class="{{ $dialogClasses }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if (isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
