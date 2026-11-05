<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased overflow-hidden">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">

        {{-- OVERLAY MOBILE --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-40 md:hidden" x-cloak>
        </div>

        {{-- SIDEBAR --}}
        <aside
            class="
            w-64 bg-white border-r
            fixed inset-y-0 left-0 z-50
            transform transition-transform
            -translate-x-full
            md:translate-x-0 md:static
        "
            :class="sidebarOpen ? 'translate-x-0' : ''">
            @include('layouts.sidebar')
        </aside>

        {{-- MAIN AREA --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR (STICKY, TIDAK SCROLL) --}}
            <header
                class="sticky top-0 z-30 h-14 bg-gray-600 border-b border-gray-500 flex items-center px-4 gap-4 text-white">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-xl text-white hover:text-gray-200">
                    ☰
                </button>

                <div class="flex-1 text-sm text-white [&_*]:text-white">
                    {{ $breadcrumb ?? '' }}
                </div>


                <x-dropdown align="right">
                    <x-slot name="trigger">
                        <button class="text-sm font-medium text-white hover:text-gray-200">
                            {{ Auth::user()->name }}
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link href="{{ route('profile.edit') }}">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </header>


            {{-- PAGE HEADER (IKUT DIAM) --}}
            @isset($pageHeader)
                <div class="sticky top-14 z-20 bg-white border-b px-6 py-4">
                    {{ $pageHeader }}
                </div>
            @endisset

            {{-- SCROLL AREA (SATU-SATUNYA YANG SCROLL) --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>

        </div>
    </div>

</body>

</html>
