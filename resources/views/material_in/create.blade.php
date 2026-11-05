<x-app-layout>
    @slot('title', 'Tambah Material Masuk')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-in.index') }}">Material Masuk</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah Material Masuk</span>
        </nav>
    @endslot

    <x-page.header title="Tambah Material Masuk" subtitle="Isi data material masuk dengan lengkap">
        <x-button.secondary :href="route('material-in.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="bg-white rounded-xl shadow p-6 mx-auto">
        <form action="{{ route('material-in.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Baris 1: Pilih Material | Qty --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Material</label>
                    <select name="material_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        @foreach ($materials as $m)
                            <option value="{{ $m->id }}">{{ $m->material_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                    <input type="number" name="qty" min="1" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>
            </div>

            {{-- Baris 2: Source | Invoice --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Material</label>
                    <input type="text" name="source" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice / Surat Jalan</label>
                    <input type="text" name="invoice_number"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <x-button.primary type="submit">Simpan</x-button.primary>
                <x-button.secondary :href="route('material-in.index')">Batal</x-button.secondary>
            </div>
        </form>
    </div>

</x-app-layout>
