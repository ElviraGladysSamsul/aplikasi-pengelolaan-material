<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'px-4 py-2 bg-red-600 text-gray-100 rounded-lg text-sm hover:bg-red-700',
    ]) }}>
    {{ $slot }}
</button>
