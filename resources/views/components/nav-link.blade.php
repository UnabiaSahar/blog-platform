@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active fw-semibold'
            : 'nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} {{ (($active ?? false)) ? 'aria-current=page' : '' }}>
    {{ $slot }}
</a>