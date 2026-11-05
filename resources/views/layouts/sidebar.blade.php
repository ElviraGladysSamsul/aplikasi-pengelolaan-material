@php
    $role = auth()->user()->role;
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r
           transform transition-transform duration-200
           md:static md:translate-x-0"
    :class="{
        '-translate-x-full': !sidebarOpen,
        'translate-x-0': sidebarOpen
    }">

    {{-- LOGO --}}
    <div class="h-14 flex items-center px-6 border-b font-bold text-white bg-gray-600">
        {{ config('app.name') }}
    </div>

    {{-- MENU --}}
    <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-y-auto">

        {{-- DASHBOARD --}}
        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-sidebar-link>

        {{-- ================= MATERIAL ================= --}}
        <x-sidebar-title>Material</x-sidebar-title>

        {{-- Semua role --}}
        <x-sidebar-link href="{{ route('materials.index') }}" :active="request()->routeIs('materials.*')">
            Daftar Material
        </x-sidebar-link>

        {{-- Hanya admin / superadmin / supervisor --}}
        @if (in_array($role, ['superadmin', 'admin', 'supervisor']))
            <x-sidebar-link href="{{ route('material-in.index') }}" :active="request()->routeIs('material-in.*')">
                Material Masuk
            </x-sidebar-link>
        @endif

        {{-- Semua role (data difilter di controller) --}}
        <x-sidebar-link href="{{ route('material-out.index') }}" :active="request()->routeIs('material-out.*')">
            Distribusi Material
        </x-sidebar-link>

        <x-sidebar-link href="{{ route('material-usages.index') }}" :active="request()->routeIs('material-usages.*')">
            Pemakaian Material (WO)
        </x-sidebar-link>

        <x-sidebar-link href="{{ route('material-returns.index') }}" :active="request()->routeIs('material-returns.*')">
            Pengembalian Material
        </x-sidebar-link>

        <x-sidebar-link href="{{ route('damaged-material.index') }}" :active="request()->routeIs('damaged-material.*')">
            Material Rusak
        </x-sidebar-link>

        {{-- ================= MANAJEMEN ================= --}}
        <x-sidebar-title>Manajemen</x-sidebar-title>

        {{-- TEAM --}}
        @if ($role === 'teknisi')
            {{-- 🔥 teknisi langsung ke show team --}}
            <x-sidebar-link href="{{ route('teams.show', auth()->user()->team_id) }}" :active="request()->routeIs('teams.show')">
                Team
            </x-sidebar-link>
        @else
            <x-sidebar-link href="{{ route('teams.index') }}" :active="request()->routeIs('teams.*')">
                Team
            </x-sidebar-link>
        @endif

        {{-- USER (hanya admin & superadmin) --}}
        @if (in_array($role, ['superadmin', 'admin', 'supervisor']))
            <x-sidebar-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                User
            </x-sidebar-link>
        @endif

    </nav>
</aside>
