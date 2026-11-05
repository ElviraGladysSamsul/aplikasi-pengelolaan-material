@props(['type' => 'button', 'href' => null])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'px-4 py-2 bg-gray-200 text-gray-700 text-center text-sm rounded-lg hover:bg-gray-300 transition']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => 'px-4 py-2 bg-gray-200 text-gray-700 text-center text-sm rounded-lg hover:bg-gray-300 transition']) }}>
        {{ $slot }}
    </button>
@endif
