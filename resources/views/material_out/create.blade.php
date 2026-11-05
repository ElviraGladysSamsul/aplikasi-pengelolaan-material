<x-app-layout>
    @slot('title', 'Tambah Distribusi Material Tim')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-out.index') }}">Distribusi Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah Distribusi Material</span>
        </nav>
    @endslot

    <x-page.header title="Tambah Distribusi Material Tim" subtitle="Isi data material masuk dengan lengkap">
        <x-button.secondary :href="route('material-out.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div>
        <div class="mx-auto">
            <div class="bg-white p-6 rounded-xl shadow-md">

                <form action="{{ route('material-out.store') }}" method="POST" id="materialForm">
                    @csrf

                    {{-- PILIH TIM --}}
                    <div class="mb-4">
                        <label class="font-semibold">Pilih Tim</label>
                        <select name="team_id" class="w-full border rounded-lg p-2" required>
                            @foreach ($teams as $t)
                                <option value="{{ $t->id }}">{{ $t->team_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="font-bold text-lg mb-2">Material</h3>

                    {{-- WARNING SEMUA MATERIAL HABIS --}}
                    <div id="materialWarning"
                        class="hidden mb-3 text-sm text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg p-2">
                        Semua material sudah dipilih.
                    </div>

                    <div id="itemWrapper">
                        <div class="grid grid-cols-3 gap-3 mb-3 itemRow">
                            <select name="materials[0][material_id]" class="material-select border rounded-lg p-2"
                                required></select>

                            <div>
                                <input type="number" name="materials[0][quantity]"
                                    class="qty-input border rounded-lg p-2 w-full" placeholder="Jumlah" min="1"
                                    required>

                                <p class="qty-warning hidden text-xs text-red-600 mt-1"></p>
                            </div>

                            <button type="button" class="removeRow bg-red-500 text-white px-3 rounded-lg">
                                X
                            </button>
                        </div>
                    </div>
                    <div class="py-3">
                        <button type="button" id="addRow" class="bg-green-600 text-white px-4 py-2 rounded-lg mb-4">
                            + Tambah Material
                        </button>

                        <button type="submit" id="submitBtn" class="bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Simpan Distribusi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- DATA MATERIAL + STOK --}}
    <script>
        const allMaterials = {!! json_encode(
            $materials->map(
                    fn($m) => [
                        'id' => $m->id,
                        'name' => $m->material_name,
                        'stock' => $m->stock,
                    ],
                )->values(),
        ) !!};
    </script>

    {{-- SCRIPT --}}
    <script>
        let index = 1;
        const addRowBtn = document.getElementById('addRow');
        const submitBtn = document.getElementById('submitBtn');
        const warningBox = document.getElementById('materialWarning');

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.material-select'))
                .map(s => s.value)
                .filter(Boolean);
        }

        function refreshMaterialOptions() {
            const selected = getSelectedIds();

            document.querySelectorAll('.material-select').forEach(select => {
                const current = select.value;
                select.innerHTML = '<option value="">-- Pilih Material --</option>';

                allMaterials.forEach(m => {
                    if (!selected.includes(String(m.id)) || String(m.id) === current) {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = `${m.name} (Sisa: ${m.stock})`;
                        opt.dataset.stock = m.stock;
                        if (String(m.id) === current) opt.selected = true;
                        select.appendChild(opt);
                    }
                });
            });

            toggleAddButton();
            toggleSubmitButton();
        }

        function toggleAddButton() {
            if (getSelectedIds().length >= allMaterials.length) {
                addRowBtn.disabled = true;
                addRowBtn.classList.add('opacity-50');
                warningBox.classList.remove('hidden');
            } else {
                addRowBtn.disabled = false;
                addRowBtn.classList.remove('opacity-50');
                warningBox.classList.add('hidden');
            }
        }

        function validateQty(row) {
            const select = row.querySelector('.material-select');
            const qtyInput = row.querySelector('.qty-input');
            const warning = row.querySelector('.qty-warning');

            if (!select.value || !qtyInput.value) return false;

            const stock = Number(select.selectedOptions[0]?.dataset.stock ?? 0);
            const qty = Number(qtyInput.value);

            if (qty > stock || qty <= 0) {
                qtyInput.classList.add('border-red-500');
                warning.textContent = `Jumlah melebihi stok tersedia (${stock})`;
                warning.classList.remove('hidden');
                return false;
            } else {
                qtyInput.classList.remove('border-red-500');
                warning.classList.add('hidden');
                return true;
            }
        }

        function toggleSubmitButton() {
            let valid = true;

            document.querySelectorAll('.itemRow').forEach(row => {
                if (!validateQty(row)) valid = false;
            });

            submitBtn.disabled = !valid;
            submitBtn.classList.toggle('opacity-50', !valid);
        }

        addRowBtn.addEventListener('click', () => {
            if (addRowBtn.disabled) return;

            const wrapper = document.getElementById('itemWrapper');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-3 gap-3 mb-3 itemRow';

            row.innerHTML = `
                <select name="materials[${index}][material_id]"
                    class="material-select border rounded-lg p-2" required></select>

                <div>
                    <input type="number" name="materials[${index}][quantity]"
                        class="qty-input border rounded-lg p-2 w-full"
                        placeholder="Jumlah" min="1" required>

                    <p class="qty-warning hidden text-xs text-red-600 mt-1"></p>
                </div>

                <button type="button"
                    class="removeRow bg-red-500 text-white px-3 rounded-lg">X</button>
            `;

            wrapper.appendChild(row);
            index++;
            refreshMaterialOptions();
        });

        document.addEventListener('change', e => {
            if (e.target.classList.contains('material-select')) {
                refreshMaterialOptions();
            }
        });

        document.addEventListener('input', e => {
            if (e.target.classList.contains('qty-input')) {
                toggleSubmitButton();
            }
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('.itemRow').remove();
                refreshMaterialOptions();
            }
        });

        document.getElementById('materialForm').addEventListener('submit', e => {
            if (submitBtn.disabled) e.preventDefault();
        });

        refreshMaterialOptions();
    </script>
</x-app-layout>
