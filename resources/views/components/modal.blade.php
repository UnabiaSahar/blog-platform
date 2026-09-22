@props([
    'name',
    'show' => false,
])

<div class="modal fade" id="{{ $name }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>

@if ($show)
    @push('scripts')
        <script>
            new bootstrap.Modal(document.getElementById('{{ $name }}')).show();
        </script>
    @endpush
@endif