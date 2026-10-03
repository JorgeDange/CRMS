@props([
    'label' => null,
    'required' => false,
    'error' => null,
])

<div class="mb-3">
    @if ($label)
        <label class="form-label">{{ $label }}@if ($required) <span class="text-danger">*</span>@endif</label>
    @endif
    <input {{ $attributes->merge(['class' => 'form-control']) }}@if($required) required @endif>
    @if ($error)
        <div class="invalid-feedback d-block">{{ $error }}</div>
    @endif
</div>
