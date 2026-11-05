{{-- <x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- FILTER --}}
    {{-- <form method="GET" class="flex gap-2 mb-6">
        <input type="date" name="start_date" value="{{ request('start_date') }}">
        <input type="date" name="end_date" value="{{ request('end_date') }}">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
    </form> --}}

    {{-- CARD --}}
    {{-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <x-dashboard.card title="Total Material" :value="$data['total_material']" />
        <x-dashboard.card title="Material Tersedia" :value="$data['material_available']" />
        <x-dashboard.card title="Material Hampir Habis" :value="$data['material_low_stock']" />

        <x-dashboard.card title="Material Masuk" :value="$data['trx_material_in']" />

        @if(!is_null($data['trx_distribution']))
            <x-dashboard.card title="Distribusi" :value="$data['trx_distribution']" />
        @endif

        <x-dashboard.card title="Pemakaian" :value="$data['trx_usage']" />
        <x-dashboard.card title="Pengembalian" :value="$data['trx_return']" />
        <x-dashboard.card title="Material Rusak" :value="$data['trx_damaged']" />

        <x-dashboard.card title="{{ auth()->user()->role === 'teknisi' ? 'Jumlah Anggota Tim' : 'Jumlah Tim' }}"
            :value="$data['total_team']" />
    </div>
    @if(!$isTeknisi)
    <x-dashboard.card title="Jumlah Tim" :value="$totalTeamOrUser" />
    <x-dashboard.card title="Jumlah User" :value="$totalUser" />
@else
    <x-dashboard.card title="Jumlah Anggota Tim" :value="$totalTeamOrUser" />
@endif

</x-app-layout> --}} 
