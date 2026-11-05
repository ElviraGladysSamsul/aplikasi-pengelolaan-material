<x-app-layout>
    @slot('title', 'Material Masuk')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Material Masuk</span>
        </nav>
    @endslot

    {{-- HEADER --}}
    <x-page.header title="Material Masuk" subtitle="Kelola seluruh material masuk" class="pb-3">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:flex lg:items-center gap-2 w-full">

            {{-- SEARCH --}}
            <x-form.search placeholder="Cari Material..." id="search" class="text-xs h-8" />

            {{-- DATE RANGE --}}
            <x-form.input type="text" name="date_range" placeholder="Pilih Tanggal" class="text-sm h-9 w-auto"
                flatpickr />

            {{-- TAMBAH --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <x-button.primary size="sm" :href="route('material-in.create')">
                    + Tambah
                </x-button.primary>
            @endif

            {{-- EXPORT --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'supervisor']))
                <x-button.primary size="sm" :href="route('material-in.export', request()->query())">
                    📤 Export
                </x-button.primary>
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

    {{-- TABLE --}}
    <x-table>
        <x-table.head>
            <th class="p-3 text-left">No</th>
            <th class="p-2 text-left">Material</th>
            <th class="p-2 text-left">Jumlah</th>
            <th class="p-2 text-left">Satuan</th>
            <th class="p-2 text-left">Asal</th>
            <th class="p-2 text-left">Invoice</th>
            <th class="p-2 text-left">Penerima</th>
            <th class="p-2 text-left">Tanggal</th>

            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <th class="p-2 text-center">Aksi</th>
            @endif
        </x-table.head>

        <tbody class="[&>tr:nth-child(even)]:bg-gray-50 text-sm">
            @forelse($materialsIn as $m)
                <x-table.row>
                    <td class="p-3">
                        {{ $loop->iteration + ($materialsIn->currentPage() - 1) * $materialsIn->perPage() }}
                    </td>
                    <td class="p-2 font-medium">{{ $m->material->material_name }}</td>
                    <td class="p-2">{{ $m->qty }}</td>
                    <td class="p-2">{{ $m->material->unit }}</td>
                    <td class="p-2">{{ $m->source }}</td>
                    <td class="p-2">{{ $m->invoice_number ?? '-' }}</td>
                    <td class="p-2">{{ $m->user->name }}</td>
                    <td class="p-2">
                        {{ $m->received_at?->format('Y-m-d H:i') ?? '-' }}
                    </td>

                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <td class="p-2 text-center">
                            <form action="{{ route('material-in.destroy', $m->id) }}" method="POST"
                                onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <x-button.danger size="xs">Hapus</x-button.danger>
                            </form>
                        </td>
                    @endif
                </x-table.row>
            @empty
                <x-table.empty colspan="9" />
            @endforelse
        </tbody>

        <x-slot name="pagination">
            {{ $materialsIn->links() }}
        </x-slot>
    </x-table>

    {{-- SCRIPT --}}
    <script>
        const search = document.getElementById('search');
        const dateRange = document.querySelector('input[name="date_range"]');
        let t;

        flatpickr(dateRange, {
            mode: "range",
            dateFormat: "Y-m-d",
            onClose() {
                applyFilter();
            }
        });

        function applyFilter() {
            const params = new URLSearchParams(window.location.search);
            search.value ? params.set('q', search.value) : params.delete('q');
            dateRange.value ? params.set('date_range', dateRange.value) : params.delete('date_range');
            window.location = '?' + params.toString();
        }

        search.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(applyFilter, 400);
        });

        @if (request('date_range'))
            dateRange.value = "{{ request('date_range') }}";
        @endif

        @if (request('q'))
            search.value = "{{ request('q') }}";
        @endif
    </script>

</x-app-layout>
