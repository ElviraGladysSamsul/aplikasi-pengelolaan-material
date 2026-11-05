<x-app-layout>
    @slot('title', 'User')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">User</span>
        </nav>
    @endslot

    <x-page.header title="User" subtitle="Kelola seluruh data user">

        {{-- ================= DESKTOP & TABLET ================= --}}
        <div class="hidden lg:block w-full space-y-2">

            {{-- BARIS 1 --}}
            <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                <div></div>

                <div class="flex items-center gap-2 justify-end">
                    <x-form.search placeholder="Cari Nama / Email..." id="search" />

                    <x-form.select name="role" id="role" placeholder="Semua Role" :options="$roles" />

                    {{-- SUPERADMIN & ADMIN --}}
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <x-button.primary :href="route('users.create')" class="whitespace-nowrap">
                            + Tambah
                        </x-button.primary>
                    @endif

                    {{-- SUPERVISOR → EXPORT DI SINI --}}
                    @if (auth()->user()->role === 'supervisor')
                        <x-button.primary :href="route('users.export', request()->query())">
                            📤Export
                        </x-button.primary>
                    @endif
                </div>
            </div>

            {{-- BARIS 2 --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                    <div></div>

                    <div class="flex items-center gap-2 justify-end">

                        <x-button.primary class="whitespace-nowrap" :href="route('users.template')">
                            📄
                        </x-button.primary>

                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf

                            <input type="file" name="file" class="border rounded h-9 text-xs px-2 w-72" required>

                            <x-button.primary type="submit">
                                📥
                            </x-button.primary>
                        </form>

                        <x-button.primary :href="route('users.export', request()->query())">
                            📤
                        </x-button.primary>
                    </div>
                </div>
            @endif
        </div>

        {{-- ================= MOBILE ================= --}}
        <div class="lg:hidden space-y-3">

            {{-- SEARCH + FILTER --}}
            <div class="flex gap-2 w-full">
                <x-form.search placeholder="Cari..." id="search-mobile" />

                <x-form.select name="role" placeholder="Role" :options="$roles" />

                @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                    <x-button.primary :href="route('users.create')">
                        +
                    </x-button.primary>
                @endif

                {{-- SUPERVISOR --}}
                @if (auth()->user()->role === 'supervisor')
                    <x-button.primary :href="route('users.export', request()->query())">
                        📤
                    </x-button.primary>
                @endif
            </div>

            {{-- TEMPLATE / IMPORT / EXPORT --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <div class="flex items-center gap-2">

                    <x-button.primary class="px-3" :href="route('users.template')">
                        📄
                    </x-button.primary>

                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data"
                        class="flex items-center gap-2">
                        @csrf

                        <input type="file" name="file" class="border rounded h-9 text-xs px-2 w-40" required>

                        <x-button.primary class="px-3">
                            📥
                        </x-button.primary>
                    </form>

                    <x-button.primary class="px-3" :href="route('users.export', request()->query())">
                        📤
                    </x-button.primary>
                </div>
            @endif
        </div>

    </x-page.header>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- TABLE --}}
    <x-table>
        <x-table.head>
            <th class="p-3 text-left">No</th>
            <th class="p-2 text-left">Nama</th>
            <th class="p-2 text-left">Email</th>
            <th class="p-2 text-left">Role</th>
            <th class="p-2 text-left">Team</th>

            {{-- AKSI HANYA ADMIN --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <th class="p- text-center">Aksi</th>
            @endif
        </x-table.head>

        <tbody class="[&>tr:nth-child(even)]:bg-gray-50">
            @forelse($users as $u)
                <x-table.row>
                    <td class="p-3">
                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                    </td>
                    <td class="p-2 font-medium">{{ $u->name }}</td>
                    <td class="p-2">{{ $u->email }}</td>
                    <td class="p-2 capitalize">{{ $u->role }}</td>
                    <td class="p-2">{{ $u->team?->team_name ?? '-' }}</td>

                    {{-- AKSI --}}
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <td class="p-2 text-center">
                            <div class="flex justify-center gap-2">
                                <x-button.secondary size="sm" :href="route('users.edit', $u)">
                                    Edit
                                </x-button.secondary>

                                <form method="POST" action="{{ route('users.destroy', $u) }}"
                                    onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <x-button.danger size="sm">
                                        Hapus
                                    </x-button.danger>
                                </form>
                            </div>
                        </td>
                    @endif
                </x-table.row>
            @empty
                <x-table.empty colspan="6" />
            @endforelse
        </tbody>

        <x-slot name="pagination">
            {{ $users->links() }}
        </x-slot>
    </x-table>

    {{-- LIVE FILTER --}}
    <script>
        let t;
        const search = document.getElementById('search');
        const role = document.getElementById('role');
        const searchMb = document.getElementById('search-mobile');

        function apply() {
            const p = new URLSearchParams();

            if (search && search.value) p.set('q', search.value);
            if (role && role.value) p.set('role', role.value);
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

        if (role) role.addEventListener('change', apply);
    </script>

</x-app-layout>
