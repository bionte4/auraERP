@php
    $data = $this->getDashboardData();
    $kpis = $data['kpis'] ?? [];
    $chart = $data['chart'] ?? [];
    $goals = $data['goals'] ?? [];
    $sites = $data['sites'] ?? ['islands' => [], 'markers' => [], 'total' => 0];

    $iconPalette = [
        'blue'  => ['bg' => 'background: linear-gradient(135deg, #3b6ef6 0%, #2b54d4 100%); box-shadow: 0 6px 14px -3px rgba(59, 110, 246, 0.45);'],
        'green' => ['bg' => 'background: linear-gradient(135deg, #38a169 0%, #27794e 100%); box-shadow: 0 6px 14px -3px rgba(56, 161, 105, 0.45);'],
        'amber' => ['bg' => 'background: linear-gradient(135deg, #f6ad55 0%, #dd8b28 100%); box-shadow: 0 6px 14px -3px rgba(246, 173, 85, 0.45);'],
        'teal'  => ['bg' => 'background: linear-gradient(135deg, #31b8c6 0%, #1d92a0 100%); box-shadow: 0 6px 14px -3px rgba(49, 184, 198, 0.45);'],
    ];
@endphp

<x-filament-panels::page>
    <div class="dhonu-dashboard space-y-5">
        {{-- Top Sub-header row with Welcome & Breadcrumb trail --}}
        <div class="flex flex-wrap items-center justify-between gap-2 pb-1">
            <h2 class="text-base font-semibold text-slate-700 dark:text-slate-200 tracking-tight">
                Welcome
            </h2>
            <nav class="flex items-center gap-1.5 text-xs font-medium text-slate-400 dark:text-slate-500">
                <span class="text-slate-600 dark:text-slate-300 font-semibold">auraERP</span>
                <span>&rsaquo;</span>
                <span>Dashboards</span>
                <span>&rsaquo;</span>
                <span class="text-slate-400 dark:text-slate-400">Welcome</span>
            </nav>
        </div>

        {{-- ROW 1: 2x2 KPI Cards (Left 5 cols) + Business Performance Overview Combo Chart (Right 7 cols) --}}
        <div class="dhonu-top">
            {{-- 2x2 KPI Grid --}}
            <div class="dhonu-kpis">
                @foreach ($kpis as $kpi)
                    @php
                        $colorStyle = $iconPalette[$kpi['color']]['bg'] ?? $iconPalette['blue']['bg'];
                    @endphp
                    <a href="{{ $kpi['href'] }}" class="dhonu-card dhonu-link">
                        {{-- Card Header with Left Vertical Accent Bar --}}
                        <div class="flex items-center gap-2.5">
                            <span class="dhonu-accent-bar"></span>
                            <span class="text-[13px] font-semibold text-slate-700 dark:text-slate-200">
                                {{ $kpi['title'] }}
                            </span>
                        </div>

                        {{-- Center Icon Circle + Large Metric Value --}}
                        <div class="my-5 flex items-center justify-center gap-3.5">
                            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-white" style="{{ $colorStyle }}">
                                @if ($kpi['icon'] === 'briefcase')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                @elseif ($kpi['icon'] === 'banknotes')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif ($kpi['icon'] === 'users')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                                {{ $kpi['value'] }}
                            </span>
                        </div>

                        {{-- Bottom Trend Line --}}
                        @if (filled($kpi['change'] ?? null) || filled($kpi['period'] ?? null))
                            <div class="dhonu-kpi-foot">
                                <span class="dhonu-kpi-change">
                                    @if (filled($kpi['change'] ?? null))
                                        {{ $kpi['change'] }}
                                    @endif
                                    {{ $kpi['period'] ?? '' }}
                                </span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Business Performance Overview Combo Chart --}}
            <a href="{{ $data['links']['project'] ?? url('/admin/project') }}" class="dhonu-card dhonu-chart dhonu-link">
                <div class="dhonu-card-title">
                    <span class="dhonu-accent-bar"></span>
                    <h3>Business Performance Overview</h3>
                    <span class="dhonu-open">Buka</span>
                </div>

                <div class="relative w-full" style="height: 250px;">
                    <canvas id="dhonu-combo-chart" wire:ignore></canvas>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-center gap-5 text-xs font-medium text-slate-600 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #93b4eb;"></span>
                        Projects Created
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #4c6ef5;"></span>
                        Employees Joined
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #489e69;"></span>
                        Completion Rate
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #f6bd60;"></span>
                        Revenue Growth
                    </span>
                </div>
            </a>
        </div>

        <div class="dhonu-bottom">
            <a href="{{ $data['links']['project'] ?? url('/admin/project') }}" class="dhonu-card dhonu-panel dhonu-link">
                <div class="dhonu-card-title">
                    <span class="dhonu-accent-bar"></span>
                    <h3>Resource & Activity Matrix</h3>
                    <span class="dhonu-open">Buka</span>
                </div>

                <div class="relative w-full my-auto" style="height: 255px;">
                    <canvas id="dhonu-bubble-chart" wire:ignore></canvas>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-center gap-4 text-xs font-medium text-slate-600 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #3b6ef6;"></span>
                        Projects & Tasks
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #489e69;"></span>
                        Field Attendance
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: #f6ad55;"></span>
                        Evaluations
                    </span>
                </div>
            </a>

            <a href="{{ $data['links']['goals'] ?? url('/admin/welcome') }}" class="dhonu-card dhonu-panel dhonu-link">
                <div class="dhonu-card-title">
                    <span class="dhonu-accent-bar"></span>
                    <h3>Goal Achievement Metrics</h3>
                    <span class="dhonu-open">Buka</span>
                </div>

                {{-- Concentric Multi-Ring Radial SVG --}}
                <div class="my-2 flex items-center justify-center">
                    <svg class="w-40 h-40 -rotate-90" viewBox="0 0 160 160">
                        {{-- Outer Track & Progress (82%) --}}
                        <circle cx="80" cy="80" r="62" fill="none" stroke="currentColor" class="text-slate-100 dark:text-slate-800" stroke-width="10"/>
                        <circle cx="80" cy="80" r="62" fill="none" stroke="#5a67d8" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="389.5" stroke-dashoffset="{{ 389.5 * (1 - (($goals[0]['progress'] ?? 82) / 100)) }}"/>

                        {{-- Middle Track & Progress (66%) --}}
                        <circle cx="80" cy="80" r="45" fill="none" stroke="currentColor" class="text-slate-100 dark:text-slate-800" stroke-width="10"/>
                        <circle cx="80" cy="80" r="45" fill="none" stroke="#38b2ac" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="282.7" stroke-dashoffset="{{ 282.7 * (1 - (($goals[1]['progress'] ?? 68) / 100)) }}"/>

                        {{-- Inner Track & Progress (74%) --}}
                        <circle cx="80" cy="80" r="28" fill="none" stroke="currentColor" class="text-slate-100 dark:text-slate-800" stroke-width="10"/>
                        <circle cx="80" cy="80" r="28" fill="none" stroke="#3182ce" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="175.9" stroke-dashoffset="{{ 175.9 * (1 - (($goals[2]['progress'] ?? 74) / 100)) }}"/>
                    </svg>
                </div>

                <div class="dhonu-goals">
                    @foreach ($goals as $index => $goal)
                        @php
                            $barColor = ['#5a67d8', '#38b2ac', '#3182ce'][$index] ?? '#5a67d8';
                        @endphp
                        <div class="dhonu-goal-row">
                            <strong>{{ $goal['goal'] }}</strong>
                            <span>{{ $goal['progress'] }}%</span>
                            <div class="dhonu-goal-bar"><i style="width: {{ $goal['progress'] }}%; background: {{ $barColor }}"></i></div>
                            <em>{{ $goal['completed'] }} / {{ $goal['target'] }}</em>
                        </div>
                    @endforeach
                </div>
            </a>

            <a href="{{ $data['links']['sites'] ?? url('/admin/welcome') }}" class="dhonu-card dhonu-map dhonu-link">
                <div class="dhonu-card-title">
                    <span class="dhonu-accent-bar"></span>
                    <h3>Penempatan Karyawan</h3>
                    <span class="dhonu-open">Buka</span>
                </div>

                <div class="dhonu-map-canvas">
                    <svg viewBox="0 0 900 390" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Peta penempatan karyawan Indonesia">
                        <g fill="#b9cffc" stroke="#5b82f0" stroke-width="2" stroke-linejoin="round">
                            @foreach ($sites['islands'] ?? [] as $island)
                                <polygon points="{{ $island }}" />
                            @endforeach
                        </g>
                        @foreach ($sites['markers'] ?? [] as $marker)
                            <g>
                                <circle cx="{{ $marker['x'] }}" cy="{{ $marker['y'] }}" r="{{ max(7, min(16, 6 + $marker['count'])) }}" fill="{{ $marker['color'] }}" stroke="#ffffff" stroke-width="2" />
                                <text x="{{ $marker['x'] }}" y="{{ $marker['y'] + 3.5 }}" text-anchor="middle" fill="#ffffff" font-size="9" font-weight="700">{{ $marker['count'] }}</text>
                            </g>
                        @endforeach
                    </svg>
                </div>

                <div class="dhonu-map-legend">
                    @forelse ($sites['markers'] ?? [] as $marker)
                        <div class="dhonu-map-site">
                            <span class="dhonu-map-dot" style="background: {{ $marker['color'] }}"></span>
                            <div>
                                <strong>{{ $marker['name'] }}</strong>
                                <span>{{ $marker['count'] }} karyawan</span>
                                @if ($marker['names'] !== [])
                                    <em>{{ implode(', ', $marker['names']) }}{{ $marker['extra'] > 0 ? ' +'.$marker['extra'] : '' }}</em>
                                @else
                                    <em>Belum ada karyawan ditempatkan</em>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="dhonu-map-empty">Belum ada titik penempatan. Isi kota &amp; koordinat pada Work Location, lalu tetapkan di profil karyawan.</p>
                    @endforelse
                </div>
            </a>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    @endassets

    @script
        <script>
            const initDhonuCharts = () => {
                const comboEl = document.getElementById('dhonu-combo-chart');
                if (comboEl && window.Chart) {
                    if (comboEl._chartInstance) {
                        comboEl._chartInstance.destroy();
                    }

                    const months = @js($chart['months'] ?? []);
                    const barData = @js($chart['bar'] ?? []);
                    const activeData = @js($chart['active'] ?? []);
                    const curveData = @js($chart['curve'] ?? []);
                    const revData = @js($chart['revenueTrend'] ?? []);

                    comboEl._chartInstance = new Chart(comboEl, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    type: 'line',
                                    label: 'Completion Rate',
                                    data: curveData,
                                    borderColor: '#489e69',
                                    backgroundColor: 'rgba(72, 158, 105, 0.10)',
                                    borderWidth: 2,
                                    tension: 0.42,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 4,
                                    order: 1,
                                },
                                {
                                    type: 'line',
                                    label: 'Revenue Growth',
                                    data: revData,
                                    borderColor: '#f6bd60',
                                    borderDash: [5, 5],
                                    borderWidth: 2,
                                    tension: 0.38,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 4,
                                    order: 2,
                                },
                                {
                                    type: 'bar',
                                    label: 'Active Users',
                                    data: activeData,
                                    backgroundColor: '#4c6ef5',
                                    borderRadius: 4,
                                    barPercentage: 0.42,
                                    categoryPercentage: 0.55,
                                    order: 3,
                                },
                                {
                                    type: 'bar',
                                    label: 'Operational Volume',
                                    data: barData,
                                    backgroundColor: 'rgba(147, 180, 235, 0.38)',
                                    borderRadius: 4,
                                    barPercentage: 0.42,
                                    categoryPercentage: 0.55,
                                    order: 4,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#94a3b8', font: { size: 11 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    max: 125,
                                    grid: { color: 'rgba(148, 163, 184, 0.14)' },
                                    ticks: {
                                        stepSize: 25,
                                        color: '#94a3b8',
                                        font: { size: 11 },
                                        callback: (v) => Number(v).toFixed(2)
                                    }
                                }
                            }
                        }
                    });
                }

                const bubbleEl = document.getElementById('dhonu-bubble-chart');
                if (bubbleEl && window.Chart) {
                    if (bubbleEl._chartInstance) {
                        bubbleEl._chartInstance.destroy();
                    }

                    bubbleEl._chartInstance = new Chart(bubbleEl, {
                        type: 'bubble',
                        data: {
                            datasets: [
                                {
                                    label: 'Projects & Tasks',
                                    backgroundColor: 'rgba(59, 110, 246, 0.78)',
                                    borderColor: '#3b6ef6',
                                    data: [
                                        { x: 18, y: 22, r: 11 },
                                        { x: 85, y: 12, r: 12 },
                                        { x: 245, y: 58, r: 7 },
                                        { x: 425, y: 31, r: 13 },
                                        { x: 515, y: 14, r: 13 },
                                        { x: 635, y: 50, r: 13 },
                                        { x: 735, y: 44, r: 12 }
                                    ]
                                },
                                {
                                    label: 'Field Attendance',
                                    backgroundColor: 'rgba(72, 158, 105, 0.78)',
                                    borderColor: '#489e69',
                                    data: [
                                        { x: 235, y: 27, r: 13 },
                                        { x: 390, y: 17, r: 6 },
                                        { x: 452, y: 60, r: 11 },
                                        { x: 545, y: 36, r: 12 },
                                        { x: 550, y: 47, r: 10 },
                                        { x: 685, y: 29, r: 5 }
                                    ]
                                },
                                {
                                    label: 'Evaluations',
                                    backgroundColor: 'rgba(246, 173, 85, 0.78)',
                                    borderColor: '#f6ad55',
                                    data: [
                                        { x: 24, y: 44, r: 11 },
                                        { x: 32, y: 10, r: 9 },
                                        { x: 96, y: 49, r: 11 },
                                        { x: 538, y: 22, r: 12 },
                                        { x: 658, y: 48, r: 12 },
                                        { x: 662, y: 19, r: 7 },
                                        { x: 705, y: 34, r: 6 }
                                    ]
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    min: 0,
                                    max: 760,
                                    grid: { color: 'rgba(148, 163, 184, 0.14)' },
                                    ticks: { color: '#94a3b8', font: { size: 10 } }
                                },
                                y: {
                                    min: 0,
                                    max: 70,
                                    grid: { color: 'rgba(148, 163, 184, 0.14)' },
                                    ticks: { color: '#94a3b8', font: { size: 10 }, stepSize: 10 }
                                }
                            }
                        }
                    });
                }
            };

            setTimeout(initDhonuCharts, 80);
            document.addEventListener('livewire:navigated', () => setTimeout(initDhonuCharts, 80));
        </script>
    @endscript
</x-filament-panels::page>
