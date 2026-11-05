<x-app-layout>
    @slot('title', 'Distribusi Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Distribusi Material</span>
        </nav>
    @endslot

    {{-- HEADER --}}
    <x-page.header title="Distribusi Material" subtitle="Daftar distribusi material ke tim" class="pb-3">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:flex lg:items-center gap-2 w-full">

            {{-- SEARCH --}}
            <x-form.search id="search" placeholder="Cari tim / admin..." class="text-xs h-8" />

            {{-- DATE RANGE --}}
            <x-form.input type="text" name="date_range" placeholder="Pilih Tanggal" class="text-sm h-9 w-auto"
                flatpickr />

            {{-- TAMBAH --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <x-button.primary size="sm" :href="route('material-out.create')">
                    + Tambah
                </x-button.primary>
            @endif

            {{-- EXPORT --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'supervisor']))
                <x-button.primary size="sm" :href="route('material-out.export', request()->query())">
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

    {{-- LIST CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($outs as $out)
            <div class="bg-white border rounded-xl shadow-sm flex flex-col">

                {{-- CARD HEADER --}}
                <div class="p-3 border-b flex justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">
                            Distribusi {{ $out->id }}
                        </h3>
                        <p class="text-xs text-gray-600">
                            Tim:
                            <span class="font-medium text-gray-800">
                                {{ $out->team->team_name }}
                            </span>
                        </p>
                    </div>

                    @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                        <div class="flex gap-1">
                            <x-button.primary size="xs" :href="route('material-out.show', $out->id)">
                                Detail
                            </x-button.primary>

                            <form action="{{ route('material-out.destroy', $out->id) }}" method="POST"
                                onsubmit="return confirm('Hapus distribusi ini?')">
                                @csrf
                                @method('DELETE')
                                <x-button.danger size="xs">
                                    Hapus
                                </x-button.danger>
                            </form>
                        </div>
                    @else
                        <x-button.primary size="xs" :href="route('material-out.show', $out->id)">
                            Detail
                        </x-button.primary>
                    @endif
                </div>

                {{-- CARD BODY --}}
                <div class="p-3 text-xs text-gray-600 flex-1">
                    <p>
                        Oleh:
                        <span class="font-medium text-gray-800">
                            {{ $out->user->name }}
                        </span>
                    </p>
                    <p class="mt-1 text-gray-500">
                        {{ $out->created_at->format('Y-m-d H:i') }}
                    </p>
                </div>

            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-12">
                Tidak ada data distribusi
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $outs->links() }}
    </div>

    {{-- FILTER SCRIPT --}}
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
