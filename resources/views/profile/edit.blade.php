<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="mx-auto w-full"> {{-- full width, padding tipis --}}
            <div class="p-4 bg-white shadow sm:rounded-lg mb-4">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-4 bg-white shadow sm:rounded-lg mb-4">
                @include('profile.partials.update-password-form')
            </div>

            <div class="p-4 bg-white shadow sm:rounded-lg">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
