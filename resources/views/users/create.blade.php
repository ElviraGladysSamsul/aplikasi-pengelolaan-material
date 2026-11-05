<x-app-layout>
    @slot('title', 'Tambah User')

    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a class="hover:underline" href="{{ route('users.index') }}">User</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Tambah User</span>
        </nav>
    @endslot

    <x-page.header title="Tambah User" subtitle="Tambahkan user baru ke dalam sistem">
        <x-button.secondary :href="route('users.index')">
            ← Kembali
        </x-button.secondary>
    </x-page.header>

    <div class="bg-white rounded-xl shadow p-6 mx-auto">

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded p-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}" id="userForm" class="space-y-4">
            @csrf

            {{-- BARIS 1: NAMA | EMAIL --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Nama
                    </label>
                    <input type="text" name="name" id="nameInput" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Email
                    </label>
                    <input type="email" name="email" id="emailInput" value="{{ old('email') }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>
            </div>

            {{-- BARIS 2: PASSWORD | ROLE --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Password
                    </label>
                    <input type="password" name="password" id="passwordInput" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Role
                    </label>
                    <select name="role" id="roleSelect" required class="w-full rounded-lg border-gray-300 px-3 py-2">
                        <option value="">-- Pilih Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role') == $role)>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- TEAM --}}
            <div id="teamDiv" class="hidden">
                <label class="block text-sm font-medium mb-1">
                    Team
                </label>
                <select name="team_id" id="teamSelect" class="w-full rounded-lg border-gray-300 px-3 py-2">
                    <option value="">-- Pilih Team --</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('team_id') == $team->id)>
                            {{ $team->team_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TOMBOL --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <x-button.primary type="submit" id="submitBtn">
                    Simpan
                </x-button.primary>

                <x-button.secondary :href="route('users.index')">
                    Batal
                </x-button.secondary>
            </div>
        </form>
    </div>

    {{-- LOGIC --}}
    <script>
        const roleSelect = document.getElementById('roleSelect');
        const teamDiv = document.getElementById('teamDiv');
        const teamSelect = document.getElementById('teamSelect');

        const nameInput = document.getElementById('nameInput');
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');
        const submitBtn = document.getElementById('submitBtn');

        function updateTeamVisibility() {
            // role yang butuh team
            const needTeam = ['teknisi'];

            if (needTeam.includes(roleSelect.value)) {
                teamDiv.classList.remove('hidden');
            } else {
                teamDiv.classList.add('hidden');
                teamSelect.value = '';
            }

            validateForm();
        }

        function validateForm() {
            const valid =
                nameInput.value.trim() &&
                emailInput.value.trim() &&
                passwordInput.value.trim() &&
                roleSelect.value &&
                (
                    !['teknisi'].includes(roleSelect.value) ||
                    teamSelect.value
                );

            submitBtn.disabled = !valid;
            submitBtn.classList.toggle('opacity-50', !valid);
        }

        roleSelect.addEventListener('change', updateTeamVisibility);
        teamSelect.addEventListener('change', validateForm);
        nameInput.addEventListener('input', validateForm);
        emailInput.addEventListener('input', validateForm);
        passwordInput.addEventListener('input', validateForm);

        // INIT
        updateTeamVisibility();
        submitBtn.disabled = true;
    </script>
</x-app-layout>
