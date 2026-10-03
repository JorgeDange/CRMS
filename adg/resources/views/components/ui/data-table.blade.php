@props(['id' => null])

<div class="table-responsive">
    <table @if($id) id="{{ $id }}" @endif {{ $attributes->merge(['class' => 'table table-hover']) }}>
        <thead>
            {{ $thead ?? '' }}
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
