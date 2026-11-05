<x-app-layout>
    @slot('title', 'Detail Team')

    <x-page.header title="Detail Team" subtitle="Informasi lengkap team, anggota, dan stok material">
        <div class="flex gap-2">
            @if (in_array(auth()->user()->role, ['superadmin', 'admin']))
                <x-button.primary :href="route('teams.edit', $team->id)">
                    Edit Team
                </x-button.primary>
            @endif
            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'supervisor']))
                <x-button.secondary :href="route('teams.index')">
                    Kembali
                </x-button.secondary>
            @endif
        </div>
    </x-page.header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: INFO + ANGGOTA --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">

            {{-- HEADER --}}
            <div class="bg-gray-600 border-b border-gray-100 px-5 py-3">
                <h3 class="font-semibold text-gray-100">
                    Informasi & Anggota Team
                </h3>
            </div>

            <div class="p-5 space-y-6 text-sm">

                {{-- INFO TEAM --}}
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-gray-500">Nama Team</div>
                            <div class="font-medium text-base">
                                {{ $team->team_name }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Mitra</div>
                            <div class="font-medium">
                                {{ $team->mitra ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-gray-500">Deskripsi</div>
                        <div class="leading-relaxed">
                            {{ $team->description ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- DIVIDER --}}
                <hr>

                {{-- ANGGOTA TEAM --}}
                <div>
                    <div class="bg-gray-200 py-2 rounded-lg flex justify-between items-center mb-3">
                        <h4 class="px-3 font-semibold">
                            Anggota Team
                        </h4>
                        <span class="px-4 text-xs text-gray-500">
                            Total: {{ $team->users->count() }} orang
                        </span>
                    </div>

                    @if ($team->users->count())
                        <div class="space-y-2">
                            @foreach ($team->users as $user)
                                <div class="flex justify-between items-center border rounded-lg p-3">
                                    <div>
                                        <div class="font-medium">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $user->email }}
                                        </div>
                                    </div>

                                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700 capitalize">
                                        {{ $user->role }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm italic text-gray-500">
                            Belum ada anggota di team ini.
                        </p>
                    @endif
                </div>

            </div>
        </div>

        {{-- KANAN: STOK --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow overflow-hidden">

                <div class="bg-gray-600 border-b border-gray-100 px-5 py-3 flex justify-between">
                    <h3 class="font-semibold text-gray-100">
                        Stok Material Team
                    </h3>
                    <span class="px-3 text-xs text-gray-100">
                        Total: {{ $team->stocks->count() }} material
                    </span>
                </div>

                <div class="p-5">
                    @if ($team->stocks->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="p-3 text-left rounded-tl-lg">Material</th>
                                        <th class="p-3 text-right w-32 rounded-tr-lg">Jumlah</th>
                                    </tr>
                                </thead>

                                <tbody class="">
                                    @foreach ($team->stocks as $item)
                                        <tr
                                            class="border-b odd:bg-white even:bg-gray-50 hover:even:bg-gray-100 hover:odd:bg-gray-100 transition">
                                            <td class="p-3 font-medium">
                                                {{ $item->material->material_name }}
                                            </td>
                                            <td class="p-3 text-right">
                                                {{ $item->quantity }} {{ $item->material->unit }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm italic text-gray-500">
                            Team ini belum memiliki stok material.
                        </p>
                    @endif
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
