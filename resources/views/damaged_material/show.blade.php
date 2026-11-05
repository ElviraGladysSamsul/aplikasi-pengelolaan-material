<x-app-layout>
    @slot('title', 'Detail Material Rusak')

    <x-page.header title="Detail Material Rusak" subtitle="Laporan kerusakan material">
        <x-button.secondary :href="route('damaged-material.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="max-w-5xl mx-auto space-y-5">

        {{-- HEADER --}}
        <div class="bg-gray-600 rounded-xl shadow p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm opacity-80">Material Rusak</div>
                    <div class="text-2xl font-semibold">
                        {{ $damagedMaterial->material->material_name }}
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-sm opacity-80">Jumlah</div>
                    <div class="text-3xl font-bold">
                        {{ $damagedMaterial->qty }} {{ $damagedMaterial->material->unit }}
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- SUMBER --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7h18M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Sumber</div>
                    <div class="text-lg font-semibold text-gray-800">
                        @if ($damagedMaterial->source === 'warehouse')
                            Gudang
                        @else
                            Tim — {{ $damagedMaterial->team?->team_name }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- PENYEBAB --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-rose-100 text-rose-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Penyebab</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $damagedMaterial->cause }}
                    </div>
                </div>
            </div>

            {{-- DILAPORKAN OLEH --}}
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="bg-emerald-100 text-emerald-600 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Dilaporkan Oleh</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $damagedMaterial->reporter->name }}
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
                    <div class="text-sm text-gray-500">Tanggal Laporan</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($damagedMaterial->reported_at)->translatedFormat('d M Y H:i') }}
                    </div>
                </div>
            </div>

        </div>

        {{-- CATATAN --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="text-sm text-yellow-700 mb-1">Catatan</div>
            <div class="text-base font-medium text-gray-800">
                {{ $damagedMaterial->notes ?? '-' }}
            </div>
        </div>

    </div>
</x-app-layout>
