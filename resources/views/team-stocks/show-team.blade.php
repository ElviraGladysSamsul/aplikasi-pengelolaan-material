<x-app-layout>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">
            Stok Team: {{ $team->team_name }}
        </h2>

        <div class="mb-4">
            <a href="{{ route('team-stocks.index') }}" class="px-3 py-2 bg-gray-600 text-white rounded">
                ← Kembali
            </a>
        </div>

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">Material</th>
                    <th class="border p-2 text-center">Stok</th>
                    <th class="border p-2 text-left">Diupdate Oleh</th>
                    <th class="border p-2 text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $item)
                    <tr>
                        <td class="border p-2">{{ $item->material->name }}</td>
                        <td class="border p-2 text-center">{{ $item->quantity }}</td>
                        <td class="border p-2">{{ $item->updater->name ?? '-' }}</td>
                        <td class="border p-2 text-center">{{ $item->updated_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($stocks->isEmpty())
            <div class="text-center p-4 text-gray-500">
                Tidak ada stok untuk team ini.
            </div>
        @endif

    </div>
</x-app-layout>
