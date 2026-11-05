<x-app-layout>
    @slot('title', 'Pengembalian Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Pengembalian Material</span>
        </nav>
    @endslot

    {{-- PAGE HEADER --}}
    <x-page.header title="Pengembalian Material" subtitle="Kelola material yang dikembalikan dari tim">

        <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">

            <x-form.search id="search" placeholder="Cari Material / Tim..." />

            <x-form.input type="daterange" name="date_range" class="w-auto" placeholder="Pilih tanggal" flatpickr />

            {{-- TAMBAH --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <x-button.primary size="sm" :href="route('material-returns.create')">
                    + Tambah
                </x-button.primary>
            @endif

            {{-- EXPORT --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'supervisor']))
                <x-button.primary size="sm" :href="route('material-returns.export', request()->query())">
                    📤 Export
                </x-button.primary>
            @endif
        </div>
    </x-page.header>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- TABLE --}}
    <x-table>
        <x-table.head>
            <th class="p-3 text-left">No</th>
            <th class="p-2 text-left">Tim</th>
            <th class="p-2 text-left">Material</th>
            <th class="p-2 text-center">Jumlah</th>
            <th class="p-2 text-center">Satuan</th>
            <th class="p-2 text-left">Penerima</th>
            <th class="p-2 text-left">Tanggal</th>
            <th class="p-2 text-center">Aksi</th>
        </x-table.head>

        <tbody class="[&>tr:nth-child(even)]:bg-gray-50">
            @forelse($data as $d)
                <x-table.row>
                    <td class="p-3">
                        {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                    </td>

                    <td class="p-2 font-medium">
                        {{ $d->team->team_name }}
                    </td>

                    <td class="p-2">
                        {{ $d->material->material_name }}
                    </td>

                    <td class="p-2 text-center">
                        {{ $d->qty }}
                    </td>

                    <td class="p-2 text-center">
                        {{ $d->material->unit }}
                    </td>

                    <td class="p-2">
                        {{ $d->admin->name }}
                    </td>

                    <td class="p-2">
                        {{ $d->returned_at?->format('Y-m-d H:i') }}
                    </td>

                    <td class="p-2 text-center">
                        <div class="flex justify-center gap-1">

                            <x-button.primary :href="route('material-returns.show', $d->id)">
                                Detail
                            </x-button.primary>

                            {{-- HAPUS --}}
                            @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
                                <form method="POST" action="{{ route('material-returns.destroy', $d->id) }}"
                                    onsubmit="return confirm('Hapus & rollback stok?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button.danger>
                                        Hapus
                                    </x-button.danger>
                                </form>
                            @endif

                        </div>
                    </td>
                </x-table.row>
            @empty
                <x-table.empty colspan="8" />
            @endforelse
        </tbody>

        <x-slot name="pagination">
            {{ $data->links() }}
        </x-slot>
    </x-table>

    {{-- FILTER SCRIPT --}}
    <script>
        const search = document.getElementById('search');
        const dateRange = document.querySelector('input[name="date_range"]');
        let t;

        flatpickr(dateRange, {
            mode: "range",
            dateFormat: "Y-m-d",
            onClose: applyFilter
        });

        function applyFilter() {
            const params = new URLSearchParams(window.location.search);

            search.value ?
                params.set('q', search.value) :
                params.delete('q');

            dateRange.value ?
                params.set('date_range', dateRange.value) :
                params.delete('date_range');

            window.location = '?' + params.toString();
        }

        search.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(applyFilter, 400);
        });

        @if (request('q'))
            search.value = "{{ request('q') }}";
        @endif

        @if (request('date_range'))
            dateRange.value = "{{ request('date_range') }}";
        @endif
    </script>

</x-app-layout>
