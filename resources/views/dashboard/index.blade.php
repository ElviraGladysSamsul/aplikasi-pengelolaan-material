<x-app-layout>
    @slot('title', 'Dashboard')
    @slot('breadcrumb')
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <span class="font-medium text-gray-800">Dashboard</span>
        </nav>
    @endslot

    {{-- ================= CARD GRID ================= --}}
    <div
        class="grid {{ $isTeknisi || $selectedTeamId ? 'grid-cols-2 sm:grid-cols-4' : 'grid-cols-2 sm:grid-cols-5' }} gap-4 mb-5">
        @php
            $cards =
                $isTeknisi || $selectedTeamId
                    ? [
                        [
                            'title' => 'Total Material',
                            'value' => $totalMaterial,
                            'icon' => 'box',
                            'color' => 'bg-blue-200 text-blue-700',
                        ],
                        [
                            'title' => 'Material Tersedia',
                            'value' => $materialAvailable,
                            'icon' => 'check',
                            'color' => 'bg-green-200 text-green-700',
                        ],
                        [
                            'title' => 'Material Hampir Habis',
                            'value' => $materialLowStock,
                            'icon' => 'warning',
                            'color' => 'bg-yellow-100 text-yellow-700',
                        ],
                        [
                            'title' => 'Anggota Tim',
                            'value' => $totalTeamOrUser,
                            'icon' => 'users',
                            'color' => 'bg-purple-100 text-purple-700',
                        ],
                    ]
                    : [
                        [
                            'title' => 'Total Material',
                            'value' => $totalMaterial,
                            'icon' => 'box',
                            'color' => 'bg-blue-200 text-blue-700',
                        ],
                        [
                            'title' => 'Material Tersedia',
                            'value' => $materialAvailable,
                            'icon' => 'check',
                            'color' => 'bg-green-200 text-green-700',
                        ],
                        [
                            'title' => 'Material Hampir Habis',
                            'value' => $materialLowStock,
                            'icon' => 'warning',
                            'color' => 'bg-yellow-100 text-yellow-700',
                        ],
                        [
                            'title' => 'Jumlah Tim',
                            'value' => $totalTeamOrUser,
                            'icon' => 'users',
                            'color' => 'bg-purple-100 text-purple-700',
                        ],
                        [
                            'title' => 'Jumlah User',
                            'value' => $totalUser,
                            'icon' => 'user',
                            'color' => 'bg-red-200 text-red-700',
                        ],
                    ];
        @endphp

        @foreach ($cards as $card)
            <div class="flex items-center p-3 shadow rounded-lg {{ $card['color'] }}">
                {{-- SVG icon --}}
                <div class="w-6 h-6 mr-3">
                    @switch($card['icon'])
                        @case('box')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7l9 5 9-5v10l-9 5-9-5V7z" />
                            </svg>
                        @break

                        @case('check')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @break

                        @case('warning')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                        @break

                        @case('users')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                        @break

                        @case('user')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A5.002 5.002 0 0112 15a5.002 5.002 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        @break
                    @endswitch
                </div>
                <div>
                    <div class="text-sm font-medium">{{ $card['title'] }}</div>
                    <div class="text-lg font-bold">{{ $card['value'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= GRAFIK + FILTER TANGGAL & TIM ================= --}}
    <div class="bg-white p-6 rounded-xl shadow mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h3 class="text-lg font-semibold mb-3 sm:mb-0">Ringkasan Aktivitas</h3>
        <form method="GET" class="flex gap-2 items-end flex-wrap sm:flex-nowrap w-full sm:w-auto">
            <div class="flex-1 min-w-[120px]">
                <label class="text-xs text-gray-600 block">Dari</label>
                <input type="date" name="start_date"
                    value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}"
                    class="border rounded px-2 py-1 w-full rounded-lg">
            </div>
            <div class="flex-1 min-w-[120px]">
                <label class="text-xs text-gray-600 block">Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}"
                    class="border rounded px-2 py-1 w-full rounded-lg">
            </div>

            {{-- Dropdown tim --}}
            @if (!$isTeknisi)
                <div class="flex-1 min-w-[160px]">
                    <label class="text-xs text-gray-600 block">Tim</label>
                    <select name="team_id"
                        class="border rounded-lg px-2 py-1 w-full bg-blue text-gray-900 appearance-none">
                        <option value="">Semua Tim</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ $selectedTeamId == $team->id ? 'selected' : '' }}>
                                {{ $team->team_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex-none">
                <button type="submit" class="px-3 py-1 bg-gray-700 hover:bg-gray-800 text-white rounded-lg">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    {{-- CHART --}}
    <div class="bg-white p-6 rounded-xl shadow mb-1">
        <canvas id="dashboardChart" height="70"></canvas>
    </div>

    {{-- ================= CHART JS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('dashboardChart');
        const labels = ['Material Masuk', 'Pemakaian', 'Pengembalian', 'Material Rusak'];
        const dataValues = [
            {{ $materialIn }},
            {{ $usage }},
            {{ $return }},
            {{ $damaged }}
        ];

        @if (!$isTeknisi && !$selectedTeamId)
            labels.splice(1, 0, 'Distribusi');
            dataValues.splice(1, 0, {{ $distribution ?? 0 }});
        @endif

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah',
                    data: dataValues,
                    backgroundColor: [
                        '#3b82f6',
                        '#6366f1',
                        '#f59e0b',
                        '#10b981',
                        '#ef4444'
                    ].slice(0, dataValues.length),
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
