@props(['type' => 'button', 'href' => null])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'px-4 py-2 bg-gray-600 text-white rounded-lg text-sm text-center hover:bg-gray-700 transition']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => 'px-4 py-2 bg-gray-600 text-white rounded-lg text-sm text-center hover:bg-gray-700 transition']) }}>
        {{ $slot }}
    </button>
@endif
