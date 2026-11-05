@props(['type' => 'text', 'flatpickr' => false, 'options' => []])

<input type="text"
    {{ $attributes->merge(['class' => 'border rounded-lg px-3 py-2 text-sm w-56 focus:ring focus:ring-gray-200', 'readonly' => $flatpickr ? true : false]) }}
    data-flatpickr="{{ $flatpickr ? 'true' : 'false' }}" data-flatpickr-type="{{ $type }}"
    data-flatpickr-options='@json($options)' />

@once
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[data-flatpickr="true"]').forEach(function(el) {
                let type = el.dataset.flatpickrType || 'text';
                let options = JSON.parse(el.dataset.flatpickrOptions || '{}');

                if (type === 'daterange') options.mode = 'range';
                if (type === 'datetime') {
                    options.enableTime = true;
                    options.dateFormat = 'Y-m-d H:i';
                }
                if (!options.dateFormat) options.dateFormat = 'Y-m-d';

                flatpickr(el, options);
            });
        });
    </script>
@endpush
