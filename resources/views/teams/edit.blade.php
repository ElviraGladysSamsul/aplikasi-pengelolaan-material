<x-app-layout>
    @slot('title', 'Edit Team')

    <x-page.header title="Edit Team" subtitle="Perbarui data team">
    </x-page.header>

    <div class="bg-white rounded-xl shadow p-6 mx-auto">

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-800 p-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('teams.update', $team) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- BARIS 1: NAMA TEAM | MITRA --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Nama Team
                    </label>
                    <input type="text" name="team_name" value="{{ old('team_name', $team->team_name) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Mitra
                    </label>
                    <input type="text" name="mitra" value="{{ old('mitra', $team->mitra) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    Deskripsi
                </label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 px-3 py-2">{{ old('description', $team->description) }}</textarea>
            </div>

            {{-- BUTTON --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <x-button.primary type="submit">
                    Update
                </x-button.primary>

                <x-button.secondary :href="route('teams.index')">
                    Batal
                </x-button.secondary>
            </div>

        </form>
    </div>
</x-app-layout>
