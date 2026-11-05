<x-app-layout>
    @slot('title', 'Team Stock')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Team Stock
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">Team</th>
                            <th class="border px-3 py-2">Material</th>
                            <th class="border px-3 py-2">Qty</th>
                            <th class="border px-3 py-2">Updated By</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($teamStocks as $stock)
                            <tr>
                                <td class="border px-3 py-2">{{ $stock->team->team_name }}</td>
                                <td class="border px-3 py-2">{{ $stock->material->material_name }}</td>
                                <td class="border px-3 py-2">{{ $stock->quantity }}</td>
                                <td class="border px-3 py-2">
                                    {{ $stock->updater?->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Belum ada data stock tim</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
