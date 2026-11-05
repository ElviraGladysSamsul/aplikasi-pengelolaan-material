<x-app-layout>
    @slot('title', 'Tambah Material Rusak')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('damaged-material.index') }}">Material Rusak</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah Material Rusak</span>
        </nav>
    @endslot

    <x-page.header title="Tambah Material Rusak" subtitle="Isi data material rusak dengan lengkap">
        <x-button.secondary :href="route('damaged-material.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="bg-white rounded-xl shadow p-6 mx-auto">

        {{-- ERROR SERVER --}}
        @error('stock')
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded p-2">
                {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('damaged-material.store') }}" id="damagedForm" class="space-y-4">
            @csrf

            {{-- BARIS 1: SOURCE | TEAM --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div id="sourceDiv" class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Sumber Kerusakan
                    </label>
                    <select name="source" id="source" required class="w-full rounded-lg border-gray-300 px-3 py-2">
                        <option value="warehouse">Gudang</option>
                        <option value="team">Tim</option>
                    </select>
                </div>

                <div id="teamDiv" class="hidden">
                    <label class="block text-sm font-medium mb-1">
                        Pilih Tim
                    </label>
                    <select name="team_id" id="teamSelect" class="w-full rounded-lg border-gray-300 px-3 py-2">
                        <option value="">-- Pilih Tim --</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}">{{ $t->team_name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- BARIS 2: MATERIAL | QTY --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Pilih Material
                    </label>
                    <select name="material_id" id="materialSelect" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                        <option value="">-- Pilih Material --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Jumlah Rusak
                    </label>
                    <input type="number" name="qty" id="qtyInput" min="1" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>
            </div>

            {{-- PENYEBAB --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    Penyebab
                </label>
                <input type="text" name="cause" id="causeInput" required
                    class="w-full rounded-lg border-gray-300 px-3 py-2">
            </div>

            {{-- CATATAN --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    Catatan
                </label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 px-3 py-2"></textarea>
            </div>

            {{-- TOMBOL --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <x-button.primary type="submit" id="submitBtn">
                    Simpan
                </x-button.primary>

                <x-button.secondary :href="route('damaged-material.index')">
                    Batal
                </x-button.secondary>
            </div>

        </form>
    </div>

    {{-- DATA --}}
    <script>
        const materials = {!! json_encode(
            $materials->map(
                    fn($m) => [
                        'id' => $m->id,
                        'name' => $m->material_name,
                        'stock' => $m->stock,
                    ],
                )->values(),
        ) !!};

        const teamStocks = {!! json_encode(
            \App\Models\TeamStock::all()->groupBy('team_id')->map(
                fn($items) => $items->mapWithKeys(
                    fn($i) => [
                        $i->material_id => $i->quantity,
                    ],
                ),
            ),
        ) !!};
    </script>

    {{-- LOGIC --}}
    <script>
        const sourceSelect = document.getElementById('source');
        const sourceDiv = document.getElementById('sourceDiv');
        const teamDiv = document.getElementById('teamDiv');
        const teamSelect = document.getElementById('teamSelect');
        const materialSelect = document.getElementById('materialSelect');
        const qtyInput = document.getElementById('qtyInput');
        const causeInput = document.getElementById('causeInput');
        const submitBtn = document.getElementById('submitBtn');

        function updateSourceLayout() {
            materialSelect.innerHTML =
                '<option value="">-- Pilih Material --</option>';

            if (sourceSelect.value === 'team') {
                sourceDiv.classList.remove('sm:col-span-2');
                teamDiv.classList.remove('hidden');
                populateTeamMaterials();
            } else {
                sourceDiv.classList.add('sm:col-span-2');
                teamDiv.classList.add('hidden');
                populateWarehouseMaterials();
            }

            validateForm();
        }

        // ✅ GUDANG → ambil dari materials.stock
        function populateWarehouseMaterials() {
            materialSelect.innerHTML =
                '<option value="">-- Pilih Material --</option>';

            materials.forEach(m => {
                if (m.stock > 0) {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = `${m.name} (Sisa: ${m.stock})`;
                    opt.dataset.stock = m.stock;
                    materialSelect.appendChild(opt);
                }
            });
        }

        // ✅ TIM → HANYA yang ada di TeamStock (TANPA fallback)
        function populateTeamMaterials() {
            materialSelect.innerHTML =
                '<option value="">-- Pilih Material --</option>';

            const stockMap = teamStocks[teamSelect.value] ?? {};

            materials.forEach(m => {
                const stock = stockMap[m.id] ?? 0;

                if (stock > 0) {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = `${m.name} (Sisa: ${stock})`;
                    opt.dataset.stock = stock;
                    materialSelect.appendChild(opt);
                }
            });
        }

        function validateForm() {
            const stock = Number(
                materialSelect.selectedOptions[0]?.dataset.stock ?? 0
            );
            const qty = Number(qtyInput.value);
            const causeFilled = causeInput.value.trim().length > 0;

            const invalid = !materialSelect.value ||
                qty <= 0 ||
                qty > stock ||
                !causeFilled ||
                (sourceSelect.value === 'team' && !teamSelect.value);

            submitBtn.disabled = invalid;
            submitBtn.classList.toggle('opacity-50', invalid);
        }

        sourceSelect.addEventListener('change', updateSourceLayout);
        teamSelect.addEventListener('change', populateTeamMaterials);
        materialSelect.addEventListener('change', validateForm);
        qtyInput.addEventListener('input', validateForm);
        causeInput.addEventListener('input', validateForm);

        // INIT
        updateSourceLayout();
        submitBtn.disabled = true;
    </script>


</x-app-layout>
