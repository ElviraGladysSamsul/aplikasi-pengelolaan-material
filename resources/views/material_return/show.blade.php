<x-app-layout>
    @slot('title', 'Detail Pengembalian Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-returns.index') }}">Pengembalian Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Detail Pengembalian Material</span>
        </nav>
    @endslot

    <x-page.header title="Detail Pengembalian Material" subtitle="Pengembalian material dari tim lapangan">
        <x-button.secondary :href="route('material-returns.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="max-w-5xl mx-auto space-y-5">

        {{-- HEADER --}}
        <div class="bg-gray-600 rounded-xl shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm opacity-80">Material</div>
                    <div class="text-2xl font-semibold">
                        {{ $materialReturn->material->material_name }}
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-sm opacity-80">Jumlah</div>
                    <div class="text-3xl font-bold">
                        {{ $materialReturn->qty }} {{ $materialReturn->material->unit }}
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- TIM --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1M15 11a4 4 0 10-8 0 4 4 0 008 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Tim</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $materialReturn->team->team_name }}
                    </div>
                </div>
            </div>

            {{-- ANGGOTA --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-emerald-100 text-emerald-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Anggota Tim</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $materialReturn->reported_by_team ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- ADMIN --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-amber-100 text-amber-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 01-8 0M12 14l9 4-9 4-9-4 9-4z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Admin Penerima</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $materialReturn->admin->name }}
                    </div>
                </div>
            </div>

            {{-- TANGGAL --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-sky-100 text-sky-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Tanggal Return</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($materialReturn->returned_at)->translatedFormat('d M Y H:i') }}
                    </div>
                </div>
            </div>

        </div>

        {{-- CATATAN --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="text-sm text-yellow-700 mb-1">Catatan</div>
            <div class="text-base font-medium text-gray-800">
                {{ $materialReturn->notes ?? '-' }}
            </div>
        </div>

    </div>
</x-app-layout>
