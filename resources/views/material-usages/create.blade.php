<x-app-layout>
    @slot('title', 'Tambah Pemakaian Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-usages.index') }}">Pemakaian Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah Pemakaian Material</span>
        </nav>
    @endslot

    <x-page.header title="Tambah Pemakaian Material (WO)" subtitle="Form pemakaian material berdasarkan WO">
        <x-button.secondary :href="route('material-usages.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="p-6 mx-auto bg-white rounded-xl shadow">

        {{-- ALERT GLOBAL --}}
        <div id="globalAlert" class="hidden mb-4 rounded p-3 text-sm border"></div>

        <form method="POST" action="{{ route('material-usages.store') }}" id="materialForm">
            @csrf

            {{-- ================= PILIH TIM (SUPERADMIN) ================= --}}
            @if (auth()->user()->role === 'superadmin')
                <div class="mb-4">
                    <h3 class="font-semibold mb-2">Pilih Tim</h3>
                    <select name="team_id" id="teamSelect" class="border p-2 rounded w-full" required>
                        <option value="">-- Pilih Tim --</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">
                                {{ $team->team_name }} ({{ $team->mitra }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- ================= DATA WO ================= --}}
            <h3 class="font-semibold mb-2">Data WO</h3>
            <div class="grid grid-cols-2 gap-4 mb-2">
                <input name="no_wo" id="noWo" class="border p-2 rounded" placeholder="No WO" required>
                <input name="jenis_psb" class="border p-2 rounded" placeholder="Jenis PSB" required>
                <input name="no_reservasi" class="border p-2 rounded" placeholder="No Reservasi">
                <input name="valins_id" class="border p-2 rounded" placeholder="Valins ID">
            </div>

            <p id="woWarning" class="hidden text-sm text-red-600 mb-4">
                Nomor WO sudah digunakan
            </p>

            <hr class="my-6">

            {{-- ================= DATA TEKNIS ================= --}}
            <h3 class="font-semibold mb-2">Data Teknis</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <input name="barcode_dc" class="border p-2 rounded" placeholder="Barcode DC">
                <input name="sn_ont" class="border p-2 rounded" placeholder="SN ONT" required>
                <input name="sn_stb" class="border p-2 rounded" placeholder="SN STB">
                <input name="power_rx_ont" class="border p-2 rounded" placeholder="Power RX ONT">
            </div>

            <hr class="my-6">

            {{-- ================= LOKASI ================= --}}
            <h3 class="font-semibold mb-2">Lokasi</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <input name="odp_lat" class="border p-2 rounded" placeholder="Latitude ODP" required>
                <input name="odp_long" class="border p-2 rounded" placeholder="Longitude ODP" required>
                <input name="pelanggan_lat" class="border p-2 rounded" placeholder="Latitude Pelanggan" required>
                <input name="pelanggan_long" class="border p-2 rounded" placeholder="Longitude Pelanggan" required>
            </div>

            <hr class="my-6">

            {{-- ================= MATERIAL ================= --}}
            <h3 class="font-semibold mb-2">Material Digunakan</h3>

            <div id="materialWarning"
                class="hidden text-sm text-yellow-700 bg-yellow-100 border border-yellow-300 rounded p-2 mb-3">
                Semua material sudah dipilih
            </div>

            <div id="itemWrapper"></div>

            <button type="button" id="addRow" class="bg-green-600 text-white px-4 py-1 rounded mb-4">
                + Tambah Material
            </button>

            <hr class="my-6">

            <textarea name="catatan_mitra" class="border p-2 w-full rounded" rows="3" placeholder="Catatan Mitra"></textarea>

            <hr class="my-6">

            <button id="submitBtn" type="button" disabled class="px-6 py-2 bg-gray-600 text-white rounded opacity-50">
                Simpan Pemakaian
            </button>

        </form>
    </div>

    {{-- ================= DATA MATERIAL ================= --}}
    <script>
        window.allMaterials = @json(collect($materials)->filter(fn($m) => ($m['stock'] ?? ($m->stock ?? 0)) > 0)->values());
    </script>


    {{-- ================= SCRIPT ================= --}}
    <script>
        let index = 0;
        let woIsDuplicate = false;
        let woTimer = null;

        const wrapper = document.getElementById('itemWrapper');
        const addRowBtn = document.getElementById('addRow');
        const submitBtn = document.getElementById('submitBtn');
        const warningBox = document.getElementById('materialWarning');
        const woInput = document.getElementById('noWo');
        const woWarning = document.getElementById('woWarning');
        const alertBox = document.getElementById('globalAlert');
        const form = document.getElementById('materialForm');

        /* ================= ALERT ================= */
        function showAlert(msg) {
            alertBox.textContent = msg;
            alertBox.className =
                'mb-4 rounded p-3 text-sm border bg-red-100 text-red-700 border-red-300';
            alertBox.classList.remove('hidden');
        }

        function hideAlert() {
            alertBox.classList.add('hidden');
        }

        /* ================= MATERIAL ================= */
        function selectedIds() {
            return [...document.querySelectorAll('.material-select')]
                .map(s => s.value).filter(Boolean);
        }

        function validateRow(row) {
            const select = row.querySelector('.material-select');
            const qty = row.querySelector('.qty-input');
            const warn = row.querySelector('.qty-warning');

            if (!select.value || !qty.value) return false;

            const stock = Number(select.selectedOptions[0].dataset.stock);
            const val = Number(qty.value);

            if (val <= 0 || val > stock) {
                warn.textContent = `Jumlah melebihi stok (${stock})`;
                warn.classList.remove('hidden');
                qty.classList.add('border-red-500');
                return false;
            }

            warn.classList.add('hidden');
            qty.classList.remove('border-red-500');
            return true;
        }

        function refreshOptions() {
            const used = selectedIds();

            document.querySelectorAll('.material-select').forEach(select => {
                const current = select.value;
                select.innerHTML = '<option value="">-- Pilih Material --</option>';

                window.allMaterials.forEach(m => {
                    if (!used.includes(String(m.id)) || String(m.id) === current) {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = `${m.name} (Sisa: ${m.stock})`;
                        opt.dataset.stock = m.stock;
                        if (String(m.id) === current) opt.selected = true;
                        select.appendChild(opt);
                    }
                });
            });

            toggleButtons();
        }

        function toggleButtons() {
            const requiredFilled = [...document.querySelectorAll('[required]')]
                .every(i => i.value.trim() !== '');

            const materialValid = [...document.querySelectorAll('.itemRow')]
                .every(r => validateRow(r));

            const canSubmit =
                requiredFilled &&
                materialValid &&
                selectedIds().length > 0 &&
                !woIsDuplicate;

            submitBtn.disabled = !canSubmit;
            submitBtn.classList.toggle('opacity-50', !canSubmit);

            if (window.allMaterials.length &&
                selectedIds().length >= window.allMaterials.length) {
                addRowBtn.disabled = true;
                warningBox.classList.remove('hidden');
            } else {
                addRowBtn.disabled = false;
                warningBox.classList.add('hidden');
            }
        }

        function addRow() {
            if (!window.allMaterials.length) return;

            const row = document.createElement('div');
            row.className = 'grid grid-cols-3 gap-3 mb-3 itemRow';

            row.innerHTML = `
        <select name="materials[${index}][material_id]"
                class="material-select border p-2 rounded" required></select>

        <div>
            <input name="materials[${index}][quantity]"
                   type="number" min="1"
                   class="qty-input border p-2 rounded w-full"
                   placeholder="Jumlah" required>
            <p class="qty-warning hidden text-xs text-red-600"></p>
        </div>

        <button type="button"
                class="removeRow bg-red-500 text-white rounded px-3">X</button>
    `;

            wrapper.appendChild(row);
            index++;
            refreshOptions();
        }

        addRowBtn.onclick = addRow;

        /* ================= EVENTS ================= */
        document.addEventListener('input', toggleButtons);

        document.addEventListener('change', e => {
            if (e.target.classList.contains('material-select')) refreshOptions();
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('.itemRow').remove();
                refreshOptions();
            }
        });

        /* ================= SUPERADMIN LOAD MATERIAL ================= */
        @if (auth()->user()->role === 'superadmin')
            document.getElementById('teamSelect').addEventListener('change', async e => {
                if (!e.target.value) return;

                const res = await fetch(`/api/materials/by-team/${e.target.value}`);
                const data = await res.json();
                window.allMaterials = data.filter(m => m.stock > 0);

                wrapper.innerHTML = '';
                index = 0;
                addRow();
            });
        @endif

        /* ================= WO CHECK ================= */
        woInput.addEventListener('input', () => {
            clearTimeout(woTimer);

            woTimer = setTimeout(async () => {
                if (!woInput.value) return;

                const res = await fetch(
                    `/material-usages/check-wo?no_wo=${woInput.value}`
                );
                const data = await res.json();

                woIsDuplicate = data.exists;
                woWarning.classList.toggle('hidden', !data.exists);
                toggleButtons();
            }, 400);
        });

        /* ================= SUBMIT ================= */
        submitBtn.onclick = () => {
            if (!submitBtn.disabled) form.submit();
        };

        /* AUTO ADD ROW UNTUK USER BIASA */
        @if (auth()->user()->role === 'superadmin')
            document.getElementById('teamSelect').addEventListener('change', async e => {
                if (!e.target.value) return;

                const res = await fetch(`/api/materials/by-team/${e.target.value}`);
                const data = await res.json();

                // ⬇️ CUMA MATERIAL YANG ADA STOK
                window.allMaterials = data.filter(m => m.stock > 0);

                wrapper.innerHTML = '';
                index = 0;
                addRow();
            });
        @endif
    </script>
</x-app-layout>
