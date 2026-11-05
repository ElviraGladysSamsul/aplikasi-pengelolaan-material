@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'options' => null, // jika select
    'placeholder' => '',
])

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea name="{{ $name }}" rows="3" {{ $required ? 'required' : '' }}
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">{{ $value }}</textarea>
    @elseif($type === 'select' && $options)
        <select name="{{ $name }}" {{ $required ? 'required' : '' }}
            class="w-full rounded-lg border-gray-300 text-gray-600 focus:ring focus:ring-gray-200 px-3 py-2">
            <option value="">{{ $placeholder }}</option>
            @foreach ($options as $opt)
                <option value="{{ $opt }}" @selected($value == $opt)>{{ $opt }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
            {{ $required ? 'required' : '' }}
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
    @endif
</div>
