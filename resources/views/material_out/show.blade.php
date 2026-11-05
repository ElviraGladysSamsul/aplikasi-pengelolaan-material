<x-app-layout>
    @slot('title', 'Detail Distribusi Material')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('material-out.index') }}">Distribusi Material</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Detail Distribusi Material</span>
        </nav>
    @endslot

    <x-page.header title="Detail Distribusi Material" subtitle="Informasi distribusi material ke tim">

        <x-button.secondary :href="route('material-out.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="mx-auto">

        {{-- INFO HEADER --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Tim</div>
                    <div class="font-semibold text-gray-800">
                        {{ $materialOut->team->team_name }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Didistribusikan Oleh</div>
                    <div class="font-semibold text-gray-800">
                        {{ $materialOut->user->name }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Tanggal Distribusi</div>
                    <div class="font-semibold text-gray-800">
                        {{ $materialOut->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL MATERIAL --}}
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800">
                    Detail Material
                </h3>
            </div>

            <div class="p-6 overflow-x-auto">

                {{-- TABLE WRAPPER (INI KUNCI ROUNDING) --}}
                <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <table class="w-full text-sm border-separate border-spacing-0">
                        <thead class="bg-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-white border-b border-gray-300">
                                    Material
                                </th>
                                <th class="px-4 py-3 text-right font-semibold text-white border-b border-gray-300 w-32">
                                    Jumlah
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            @foreach ($materialOut->details as $d)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 border-b border-gray-200">
                                        {{ $d->material->material_name }}
                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 font-medium">
                                        {{ $d->quantity }} {{ $d->material->unit }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($materialOut->details->isEmpty())
                    <div class="text-center text-gray-500 py-6">
                        Tidak ada detail material
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>
