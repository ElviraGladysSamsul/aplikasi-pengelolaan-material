<x-app-layout>
    @slot('title', 'Tambah Pengembalian Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-returns.index') }}">Pengembalian Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah Pengembalian Material</span>
        </nav>
    @endslot

    <x-page.header title="Tambah Pengembalian Material" subtitle="Isi data material yang dikembalikan dari tim">
        <x-button.secondary :href="route('material-returns.index')">
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

        {{-- ERROR CLIENT --}}
        <div id="qtyWarning" class="hidden mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded p-2">
        </div>

        <form method="POST" action="{{ route('material-returns.store') }}" id="returnForm" class="space-y-4">
            @csrf

            {{-- BARIS 1: TEAM | MATERIAL --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Tim
                    </label>
                    <select name="team_id" id="teamSelect" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">-- Pilih Tim --</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}">{{ $t->team_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Material
                    </label>
                    <select name="material_id" id="materialSelect" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">-- Pilih Material --</option>
                    </select>
                </div>

            </div>

            {{-- BARIS 2: QTY | ANGGOTA --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Dikembalikan
                    </label>
                    <input type="number" name="qty" id="qtyInput" min="1" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Anggota Tim (opsional)
                    </label>
                    <input type="text" name="reported_by_team"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                </div>

            </div>

            {{-- CATATAN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan
                </label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
            </div>

            {{-- TOMBOL --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <x-button.primary type="submit" id="submitBtn">
                    Simpan
                </x-button.primary>

                <x-button.secondary :href="route('material-returns.index')">
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
        const teamSelect = document.getElementById('teamSelect');
        const materialSelect = document.getElementById('materialSelect');
        const qtyInput = document.getElementById('qtyInput');
        const qtyWarning = document.getElementById('qtyWarning');
        const submitBtn = document.getElementById('submitBtn');

        function populateMaterials() {
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

        function validateQty() {
            const stock = Number(
                materialSelect.selectedOptions[0]?.dataset.stock ?? 0
            );
            const qty = Number(qtyInput.value);

            if (!materialSelect.value || qty <= 0) {
                disableSubmit(true);
                return;
            }

            if (qty > stock) {
                qtyWarning.textContent =
                    `Jumlah melebihi stok tim (sisa ${stock})`;
                qtyWarning.classList.remove('hidden');
                disableSubmit(true);
            } else {
                qtyWarning.classList.add('hidden');
                disableSubmit(false);
            }
        }

        function disableSubmit(state) {
            submitBtn.disabled = state;
            submitBtn.classList.toggle('opacity-50', state);
        }

        teamSelect.addEventListener('change', () => {
            populateMaterials();
            validateQty();
        });

        materialSelect.addEventListener('change', validateQty);
        qtyInput.addEventListener('input', validateQty);

        document.getElementById('returnForm')
            .addEventListener('submit', e => {
                if (submitBtn.disabled) e.preventDefault();
            });

        disableSubmit(true);
    </script>

</x-app-layout>
