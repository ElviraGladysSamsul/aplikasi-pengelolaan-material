<x-app-layout>
    @slot('title', 'Detail Pemakaian Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-usages.index') }}">Pemakaian Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Detail Pemakaian Material</span>
        </nav>
    @endslot

    <x-page.header title="WO {{ $materialUsage->no_wo }}"
        subtitle="{{ $materialUsage->created_at->format('d M Y H:i') }}">
        <x-button.secondary :href="route('material-usages.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="max-w-6xl mx-auto space-y-4">

        {{-- CARD ATAS (2 KOLOM) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- KIRI : INFO + WO --}}
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Mitra</div>
                        <div class="font-medium">{{ $materialUsage->mitra }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Tim</div>
                        <div class="font-medium">
                            {{ $materialUsage->team->team_name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Teknisi</div>
                        <div class="font-medium">
                            {{ $materialUsage->technician->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Jenis PSB</div>
                        <div class="font-medium">{{ $materialUsage->jenis_psb }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Valins ID</div>
                        <div class="font-medium">
                            {{ $materialUsage->valins_id ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">No Reservasi</div>
                        <div class="font-medium">
                            {{ $materialUsage->no_reservasi ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN : TEKNIS + LOKASI --}}
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <div class="grid grid-cols-2 gap-3 text-sm">

                    <div>
                        <div class="text-gray-500">Barcode DC</div>
                        <div class="font-medium">
                            {{ $materialUsage->barcode_dc ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">SN ONT</div>
                        <div class="font-medium">
                            {{ $materialUsage->sn_ont ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">SN STB</div>
                        <div class="font-medium">
                            {{ $materialUsage->sn_stb ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Power RX</div>
                        <div class="font-medium">
                            {{ $materialUsage->power_rx_ont ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Tikor ODP</div>
                        <div class="font-medium text-xs">
                            {{ $materialUsage->odp_lat ?? '-' }},
                            {{ $materialUsage->odp_long ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Tikor Pelanggan</div>
                        <div class="font-medium text-xs">
                            {{ $materialUsage->pelanggan_lat ?? '-' }},
                            {{ $materialUsage->pelanggan_long ?? '-' }}
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- MATERIAL --}}
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-4 py-2 border-b text-sm font-semibold text-gray-700">
                Material Digunakan
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-600">
                    <tr>
                        <th class="px-3 py-2 text-left text-white">Material</th>
                        <th class="px-3 py-2 text-center text-white w-28">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materialUsage->items as $item)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ $item->material->material_name }}
                            </td>
                            <td class="px-3 py-2 text-center font-medium">
                                {{ $item->qty }} {{ $item->material->unit }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- CATATAN --}}
        <div class="bg-gray-50 border rounded-lg p-3 text-sm">
            <span class="text-gray-500">Catatan:</span>
            <span class="font-medium text-gray-800">
                {{ $materialUsage->catatan_mitra ?? '-' }}
            </span>
        </div>

    </div>
</x-app-layout>
