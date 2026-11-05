<x-app-layout>
    @slot('title', 'Team')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Team</span>
        </nav>
    @endslot

    <x-page.header title="Team" subtitle="Kelola seluruh data team">

        {{-- ================= DESKTOP & TABLET ================= --}}
        <div class="hidden lg:block w-full space-y-2">

            {{-- BARIS 1 --}}
            <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                <div></div>

                <div class="flex items-center gap-2 justify-end">
                    <x-form.search placeholder="Cari Nama Team / Mitra..." id="search" />

                    <x-form.select name="mitra" id="mitra" placeholder="Semua Mitra" :options="$mitras" />

                    {{-- SUPERADMIN & ADMIN --}}
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <x-button.primary :href="route('teams.create')" class="whitespace-nowrap">
                            + Tambah
                        </x-button.primary>
                    @endif

                    {{-- SUPERVISOR : EXPORT ONLY --}}
                    @if (auth()->user()->role === 'supervisor')
                        <x-button.primary class="whitespace-nowrap" :href="route('teams.export', request()->query())">
                            📤 Export
                        </x-button.primary>
                    @endif
                </div>
            </div>

            {{-- BARIS 2 (ADMIN ONLY) --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                    <div></div>

                    <div class="flex items-center gap-2 justify-end">

                        <x-button.primary :href="route('teams.template')">
                            📄
                        </x-button.primary>

                        <form action="{{ route('teams.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf

                            <input type="file" name="file" class="border rounded h-9 text-xs px-2 py-2 w-72"
                                required>

                            <x-button.primary type="submit">
                                📥
                            </x-button.primary>
                        </form>

                        <x-button.primary :href="route('teams.export', request()->query())">
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

                <x-form.select name="mitra" placeholder="Mitra" :options="$mitras" />

                {{-- SUPERADMIN & ADMIN --}}
                @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                    <x-button.primary :href="route('teams.create')">
                        +
                    </x-button.primary>
                @endif

                {{-- SUPERVISOR --}}
                @if (auth()->user()->role === 'supervisor')
                    <x-button.primary :href="route('teams.export', request()->query())">
                        📤
                    </x-button.primary>
                @endif
            </div>

            {{-- TEMPLATE / IMPORT / EXPORT (ADMIN ONLY) --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <div class="flex items-center gap-2">

                    <x-button.primary :href="route('teams.template')">
                        📄
                    </x-button.primary>

                    <form action="{{ route('teams.import') }}" method="POST" enctype="multipart/form-data"
                        class="flex items-center gap-2">
                        @csrf

                        <input type="file" name="file" class="border rounded h-9 text-xs px-2 w-48" required>

                        <x-button.primary>
                            📥
                        </x-button.primary>
                    </form>

                    <x-button.primary :href="route('teams.export', request()->query())">
                        📤
                    </x-button.primary>

                </div>
            @endif
        </div>

    </x-page.header>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 text-sm rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-2 text-sm rounded mb-3">
            {{ $errors->first() }}
        </div>
    @endif
    {{-- ================= TABLE ================= --}}
    <x-table>
        <x-table.head>
            <th class="p-3 text-left">No</th>
            <th class="p-2 text-left">Nama Team</th>
            <th class="p-2 text-left">Mitra</th>
            <th class="p-2 text-left">Deskripsi</th>
            <th class="p-2 text-center">Aksi</th>
        </x-table.head>

        <tbody class="[&>tr:nth-child(even)]:bg-gray-50">
            @forelse($teams as $team)
                <x-table.row>
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-2 font-medium">{{ $team->team_name }}</td>
                    <td class="p-2">{{ $team->mitra ?? '-' }}</td>
                    <td class="p-2">{{ $team->description ?? '-' }}</td>

                    <td class="p-2 text-center">
                        <div class="flex justify-center gap-2">

                            {{-- DETAIL (ALL ROLE) --}}
                            <x-button.primary size="sm" :href="route('teams.show', $team)">
                                Detail
                            </x-button.primary>

                            {{-- EDIT & HAPUS (ADMIN ONLY) --}}
                            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                                <x-button.secondary size="sm" :href="route('teams.edit', $team)">
                                    Edit
                                </x-button.secondary>

                                <form method="POST" action="{{ route('teams.destroy', $team) }}"
                                    onsubmit="return confirm('Hapus team ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <x-button.danger size="sm">
                                        Hapus
                                    </x-button.danger>
                                </form>
                            @endif

                        </div>
                    </td>
                </x-table.row>
            @empty
                <x-table.empty colspan="5" />
            @endforelse
        </tbody>

        <x-slot name="pagination">
            {{ $teams->links() }}
        </x-slot>
    </x-table>

    {{-- ================= LIVE FILTER ================= --}}
    <script>
        let t;
        const search = document.getElementById('search');
        const mitra = document.getElementById('mitra');
        const searchMb = document.getElementById('search-mobile');

        function apply() {
            const p = new URLSearchParams();

            if (search && search.value) p.set('q', search.value);
            if (mitra && mitra.value) p.set('mitra', mitra.value);
            if (searchMb && searchMb.value) p.set('q', searchMb.value);

            window.location = '?' + p.toString();
        }

        [search, searchMb].forEach(el => {
            if (!el) return;
            el.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(apply, 400);
            });
        });

        if (mitra) mitra.addEventListener('change', apply);
    </script>

</x-app-layout>
