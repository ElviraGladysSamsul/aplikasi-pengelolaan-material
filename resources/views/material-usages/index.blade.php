<x-app-layout>
    @slot('title', 'Pemakaian Material (WO)')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Pemakaian Material</span>
        </nav>
    @endslot

    <x-page.header title="Pemakaian Material (WO)" subtitle="Riwayat pemakaian material per Work Order">

        <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">

            <x-form.search id="search" placeholder="Cari WO / Mitra / Teknisi..." />

            <x-form.input type="daterange" name="date_range" class="w-auto" placeholder="Pilih tanggal" flatpickr />

            {{-- TAMBAH --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'teknisi']))
                <x-button.primary size="sm" :href="route('material-usages.create')">
                    + Tambah
                </x-button.primary>
            @endif

            {{-- EXPORT --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'supervisor']))
                <x-button.primary size="sm" :href="route('material-usages.export', request()->query())">
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

    <div class="max-w-7xl mx-auto">

        {{-- CARD LIST --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @forelse($usages as $u)
                <div class="bg-white border rounded-xl shadow-sm hover:shadow-md transition">

                    {{-- HEADER CARD --}}
                    <div class="bg-gray-600 rounded-t-lg flex justify-between items-start p-4 border-b">

                        {{-- WO INFO --}}
                        <div>
                            <p class="text-xs text-gray-50">Work Order</p>
                            <h3 class="font-semibold text-lg text-gray-100">
                                {{ $u->no_wo }}
                            </h3>
                        </div>

                        {{-- ACTION --}}
                        <div class="flex flex-col items-end gap-1 text-sm">

                            <a href="{{ route('material-usages.show', $u->id) }}"
                                class="w-20 text-center px-2 rounded
                                      bg-gray-500 text-gray-100 text-xs
                                      hover:bg-gray-400 transition">
                                Detail
                            </a>

                            {{-- HAPUS (admin & supervisor) --}}
                            @if (in_array(auth()->user()->role, ['admin', 'superadmin', 'teknisi']))
                                <form method="POST" action="{{ route('material-usages.destroy', $u->id) }}"
                                    onsubmit="return confirm('Hapus data pemakaian material ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="w-20 px-2 rounded
                                                   bg-red-600/70 text-red-100 text-xs
                                                   hover:bg-red-600/80 transition">
                                        Hapus
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>

                    {{-- BODY --}}
                    <div class="p-4 space-y-3 text-sm text-gray-700">

                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 text-xs">
                                {{ $u->mitra }}
                            </span>
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs">
                                {{ $u->jenis_psb }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <p class="text-xs text-gray-500">Tim</p>
                                <p class="font-medium">
                                    {{ $u->team->team_name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Teknisi</p>
                                <p class="font-medium">
                                    {{ $u->technician->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-2 text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $u->created_at->format('d M Y H:i') }}
                        </div>

                    </div>

                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-20">
                    Belum ada pemakaian material
                </div>
            @endforelse

        </div>

        <div class="mt-4">
            {{ $usages->links() }}
        </div>

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
