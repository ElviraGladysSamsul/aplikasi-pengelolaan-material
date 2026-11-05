// aku hilangin smua ediet

{{-- <x-app-layout>
    <div class="p-6">

        <h2 class="text-2xl font-bold mb-4">Edit Material</h2>

        <form action="{{ route('materials.update', $material->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Kode Material</label>
                <input type="text" name="material_code" value="{{ $material->material_code }}" class="w-full border p-2" required>
            </div>

            <div class="mb-3">
                <label>Nama Material</label>
                <input type="text" name="material_name" value="{{ $material->material_name }}" class="w-full border p-2" required>
            </div>

            <div class="mb-3">
                <label>Kategori</label>
                <input type="text" name="category" value="{{ $material->category }}" class="w-full border p-2" required>
            </div>

            <div class="mb-3">
                <label>Satuan</label>
                <input type="text" name="unit" value="{{ $material->unit }}" class="w-full border p-2" required>
            </div>

            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stock" value="{{ $material->stock }}" class="w-full border p-2">
            </div>

            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="description" class="w-full border p-2">{{ $material->description }}</textarea>
            </div>

            <button class="px-3 py-2 bg-blue-600 text-white rounded">
                Update
            </button>
        </form>

    </div>
</x-app-layout> --}}
