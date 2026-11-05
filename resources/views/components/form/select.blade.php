<select name="{{ $name }}" id="{{ $id ?? $name }}"
    class="border rounded-lg px py-2 text-sm text-gray-600 focus:outline-none focus:ring focus:ring-gray-200">
    <option value="">{{ $placeholder ?? 'Semua' }}</option>

    @foreach ($options as $opt)
        <option value="{{ $opt }}" @selected(request($name) == $opt)>
            {{ $opt }}
        </option>
    @endforeach
</select>
