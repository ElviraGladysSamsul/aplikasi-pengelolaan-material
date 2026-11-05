<x-app-layout>
    @slot('title', 'Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Daftar Material</span>
        </nav>
    @endslot

    <x-page.header title="Material" subtitle="Kelola seluruh data material">

        {{-- ================= DESKTOP & TABLET ================= --}}
        <div class="hidden lg:block w-full space-y-2">

            {{-- BARIS 1 --}}
            <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                <div></div>

                <div class="flex items-center gap-2 justify-end">
                    <x-form.search placeholder="Cari Kode / Nama Material..." id="search" />

                    <x-form.select name="category" id="category" placeholder="Semua Kategori" :options="$categories" />

                    {{-- SUPERADMIN & ADMIN --}}
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <x-button.primary :href="route('materials.create')" class="whitespace-nowrap">
                            + Tambah
                        </x-button.primary>
                    @endif

                    {{-- SUPERVISOR : EXPORT ONLY (POSISI TAMBAH) --}}
                    @if (auth()->user()->role === 'supervisor')
                        <x-button.primary class="whitespace-nowrap" :href="route('materials.export', request()->query())">
                            📤 Export
                        </x-button.primary>
                    @endif
                </div>
            </div>

            {{-- BARIS 2 --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                    <div></div>

                    <div class="flex items-center gap-2 justify-end">

                        <x-button.primary :href="route('materials.template')">
                            📄
                        </x-button.primary>

                        <form action="{{ route('materials.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf

                            <input type="file" name="file" class="border rounded py-2 text-sm w-auto" required>

                            <x-button.primary type="submit">📥</x-button.primary>
                        </form>

                        <x-button.primary :href="route('materials.export', request()->query())">
                            📤
                        </x-button.primary>

                    </div>
                </div>
            @endif
        </div>

        {{-- ================= MOBILE (HP) ================= --}}
        <div class="lg:hidden space-y-3">

            {{-- SEARCH + FILTER + ACTION --}}
            <div class="flex gap-2 w-full">
                <x-form.search placeholder="Cari..." id="search-mobile" />

                <x-form.select name="category" placeholder="Kategori" :options="$categories" />

                {{-- SUPERADMIN & ADMIN --}}
                @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                    <x-button.primary :href="route('materials.create')">
                        +
                    </x-button.primary>
                @endif

                {{-- SUPERVISOR --}}
                @if (auth()->user()->role === 'supervisor')
                    <x-button.primary :href="route('materials.export', request()->query())">
                        📤
                    </x-button.primary>
                @endif
            </div>

            {{-- TEMPLATE / IMPORT / EXPORT (ADMIN ONLY) --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <div class="flex items-center gap-2">

                    <x-button.primary :href="route('materials.template')">
                        📄
                    </x-button.primary>

                    <form action="{{ route('materials.import') }}" method="POST" enctype="multipart/form-data"
                        class="flex items-center gap-2">
                        @csrf

                        <input type="file" name="file" class="border rounded h-9 text-xs px-2 w-40" required>

                        <x-button.primary>
                            📥
                        </x-button.primary>
                    </form>

                    <x-button.primary :href="route('materials.export', request()->query())">
                        📤
                    </x-button.primary>

                </div>
            @endif
        </div>

    </x-page.header>

    {{-- Alert success / error --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <x-table>
        <x-table.head>
            <th class="p-3 text-left">No</th>
            <th class="p-2 text-left">Kode</th>
            <th class="p-2 text-left">Nama</th>
            <th class="p-2 text-left">Kategori</th>
            <th class="p-2 text-left">Satuan</th>
            @if (in_array(auth()->user()->role, ['supervisor', 'teknisi']))
                <th class="p-2 text-center">Stok</th>
            @endif
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <th class="p-2 text-right">Stok</th>
                <th class="p-2 text-center">Aksi</th>
            @endif
        </x-table.head>

        <tbody class="[&>tr:nth-child(even)]:bg-gray-50">
            @forelse($materials as $m)
                <x-table.row>
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-2">{{ $m->material_code }}</td>
                    <td class="p-2 font-medium">{{ $m->material_name }}</td>
                    <td class="p-2">{{ $m->category }}</td>
                    <td class="p-2">{{ $m->unit }}</td>
                    @if (in_array(auth()->user()->role, ['supervisor', 'teknisi']))
                        <td class="p-2 text-center">{{ $m->stock }}</td>
                    @endif
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <td class="p-2 text-right">{{ $m->stock }}</td>
                        <td class="p-2 text-center">
                            <form method="POST" action="{{ route('materials.destroy', $m->id) }}"
                                onsubmit="return confirmDelete({{ $m->out_details_count }}, {{ $m->stocks_count }});">
                                @csrf
                                @method('DELETE')
                                <x-button.danger>Hapus</x-button.danger>
                            </form>
                        </td>
                    @endif
                </x-table.row>
            @empty
                <x-table.empty colspan="7" />
            @endforelse
        </tbody>

        <x-slot name="pagination">
            {{ $materials->links() }}
        </x-slot>
    </x-table>

    <script>
        function confirmDelete(outCount, stockCount) {
            if (outCount > 0 || stockCount > 0) {
                alert('Material ini sedang dipakai, tidak bisa dihapus.');
                return false;
            }
            return confirm('Hapus material?');
        }

        // Live filter
        let t;
        const search = document.getElementById('search');
        const category = document.getElementById('category');

        function apply() {
            const p = new URLSearchParams();
            if (search.value) p.set('q', search.value);
            if (category.value) p.set('category', category.value);
            window.location = '?' + p.toString();
        }

        search.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(apply, 400);
        });

        category.addEventListener('change', apply);
    </script>
</x-app-layout>
