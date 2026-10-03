@props([
    'label' => null,
    'required' => false,
    'error' => null,
    'rows' => 3,
])

<div class="mb-3">
    @if ($label)
        <label class="form-label">{{ $label }}@if ($required) <span class="text-danger">*</span>@endif</label>
    @endif
    <textarea rows="{{ $rows }}" {{ $attributes->merge(['class' => 'form-control']) }}@if($required) required @endif>{{ $slot }}</textarea>
    @if ($error)
        <div class="invalid-feedback d-block">{{ $error }}</div>
    @endif
</div>
