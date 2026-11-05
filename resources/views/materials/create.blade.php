<x-app-layout>
    @slot('title', 'Tambah Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('materials.index') }}">Daftar Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah Daftar Material</span>
        </nav>
    @endslot

    <x-page.header title="Tambah Material" subtitle="Isi data material baru dengan lengkap">
        <x-button.secondary :href="route('materials.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-6">

        {{-- ALERT GLOBAL --}}
        <div id="globalAlert" class="hidden mb-4 rounded p-3 text-sm border"></div>

        <form action="{{ route('materials.store') }}" method="POST" id="materialForm" class="space-y-5">
            @csrf

            {{-- ================= BARIS 1 ================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- KODE --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Kode Material
                    </label>
                    <input type="text" name="material_code" id="materialCode" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">

                    <p id="codeWarning" class="hidden text-xs text-red-600 mt-1">
                        Kode material sudah digunakan
                    </p>
                </div>

                {{-- NAMA --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Nama Material
                    </label>
                    <input type="text" name="material_name" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>

                {{-- KATEGORI --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Kategori
                    </label>
                    <input type="text" name="category" required class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>

                {{-- SATUAN --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Satuan
                    </label>
                    <input type="text" name="unit" required class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>
            </div>

            {{-- ================= BARIS 2 ================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Stok Awal
                    </label>
                    <input type="number" name="stock" value="0" min="0"
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-medium mb-1">
                        Deskripsi (Opsional)
                    </label>
                    <textarea name="description" rows="1" class="w-full rounded-lg border-gray-300 px-3 py-2"></textarea>
                </div>
            </div>

            {{-- ================= BUTTON ================= --}}
            <div class="flex gap-2 pt-4">
                <x-button.primary id="submitBtn" type="submit" disabled>
                    Simpan
                </x-button.primary>

                <x-button.secondary :href="route('materials.index')">
                    Batal
                </x-button.secondary>
            </div>

        </form>
    </div>

    {{-- ================= SCRIPT ================= --}}
    <script>
        const codeInput = document.getElementById('materialCode');
        const codeWarn = document.getElementById('codeWarning');
        const alertBox = document.getElementById('globalAlert');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('materialForm');

        let codeIsDuplicate = false;
        let timer = null;

        function showAlert(msg) {
            alertBox.textContent = msg;
            alertBox.className =
                'mb-4 rounded p-3 text-sm border bg-red-100 text-red-700 border-red-300';
            alertBox.classList.remove('hidden');
        }

        function hideAlert() {
            alertBox.classList.add('hidden');
        }

        function toggleSubmit() {
            const requiredFilled = [...form.querySelectorAll('[required]')]
                .every(i => i.value.trim() !== '');

            submitBtn.disabled = !requiredFilled || codeIsDuplicate;
        }

        // REALTIME CHECK KODE MATERIAL
        codeInput.addEventListener('input', () => {
            clearTimeout(timer);

            timer = setTimeout(async () => {
                const code = codeInput.value.trim();

                if (!code) {
                    codeIsDuplicate = false;
                    codeWarn.classList.add('hidden');
                    codeInput.classList.remove('border-red-500');
                    hideAlert();
                    toggleSubmit();
                    return;
                }

                try {
                    const res = await fetch(
                        `/materials/check-code?material_code=${encodeURIComponent(code)}`
                    );
                    const data = await res.json();

                    if (data.exists) {
                        codeIsDuplicate = true;
                        codeWarn.classList.remove('hidden');
                        codeInput.classList.add('border-red-500');
                        showAlert('Kode material sudah digunakan');
                    } else {
                        codeIsDuplicate = false;
                        codeWarn.classList.add('hidden');
                        codeInput.classList.remove('border-red-500');
                        hideAlert();
                    }
                } catch (e) {
                    showAlert('Gagal cek kode material');
                }

                toggleSubmit();
            }, 400);
        });

        form.addEventListener('input', toggleSubmit);
    </script>

</x-app-layout>
