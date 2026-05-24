@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'badge']) }}>
        {{ $status }}
    </div>
@endif
