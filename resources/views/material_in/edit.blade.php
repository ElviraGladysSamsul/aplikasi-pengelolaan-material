{{-- <x-app-layout>
    <div class="p-6">

        <h2 class="text-2xl font-bold mb-4">Edit Material Masuk</h2>

        <form action="{{ route('material-in.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="block font-semibold">Pilih Material</label>
                <select name="material_id" class="w-full border p-2 rounded">
                    @foreach($materials as $m)
                        <option value="{{ $m->id }}"
                                {{ $item->material_id == $m->id ? 'selected' : '' }}>
                            {{ $m->material_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block font-semibold">Qty</label>
                <input type="number" name="qty" value="{{ $item->qty }}" class="w-full border p-2 rounded">
            </div>

            <div class="mb-3">
                <label class="block font-semibold">Sumber Material</label>
                <input type="text" name="source" value="{{ $item->source }}" class="w-full border p-2 rounded">
            </div>

            <div class="mb-3">
                <label class="block font-semibold">Invoice</label>
                <input type="text" name="invoice_number" value="{{ $item->invoice_number }}" class="w-full border p-2 rounded">
            </div>

            <button class="px-3 py-2 bg-blue-600 text-white rounded">
                Update
            </button>

        </form>

    </div>
</x-app-layout> --}}
