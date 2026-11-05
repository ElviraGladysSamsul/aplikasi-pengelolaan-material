@props(['active' => false])

<a {{ $attributes }}
    class="block px-3 py-2 rounded
    {{ $active ? 'bg-gray-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
    {{ $slot }}
</a>
