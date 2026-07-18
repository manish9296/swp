@extends('layouts.app')

@section('title', $state . ' Dashboard ')

@section('content')

    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h4 class="mb-0">{{ $state }}</h4>
            <div class="subtext">State-level PM-KUSUM performance</div>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-dark btn-sm">&larr; All States</a>
    </div>

    <form method="GET" action="{{ route('dashboard.state', $state) }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <label class="form-label mb-1">Generation data for last:</label>
            <select name="days" class="form-select" onchange="this.form.submit()">
                @foreach([1, 7, 15, 30, 60, 90] as $opt)
                    <option value="{{ $opt }}" @selected($days == $opt)>{{ $opt }} {{ $opt == 1 ? 'day' : 'days' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label mb-1">Or custom range (1-90 days):</label>
            <div class="input-group">
                <input type="number" name="days" min="1" max="90" value="{{ $days }}" class="form-control">
                <button class="btn btn-success" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="stat-card b-blue">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['total_farmers'] }}</div>
                <div class="stat-label">Total Farmers</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-green">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['installed'] }}</div>
                <div class="stat-label">Installed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-orange">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['total_generation'] }}</div>
                <div class="stat-label">Generation (kWh)</div>
                <div class="stat-sub">last {{ $days }} {{ $days == 1 ? 'day' : 'days' }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-teal">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ number_format($stats['total_water']) }}</div>
                <div class="stat-label">Water Discharge (L)</div>
                <div class="stat-sub">{{ $stats['total_hours'] }} pump-run hrs</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card chart-card mb-4">
        <div class="chart-card-header">
            <span class="icon-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </span>
            Daily Generation Trend — {{ $state }} (last {{ $days }} {{ $days == 1 ? 'day' : 'days' }})
        </div>
        <div class="chart-card-body">
            <canvas id="stateTrendChart" height="200"></canvas>
        </div>
    </div>

    <div class="card chart-card mb-4">
        <div class="chart-card-header">
            <span class="icon-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
            </span>
            District-wise Breakdown
        </div>
        <div class="chart-card-body">
            <div class="table-responsive">
                <table class="table table-bordered bg-white align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>District</th>
                            <th>Generation (kWh)</th>
                            <th>Pump Run Hours</th>
                            <th>Water Discharge (L)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($districtBreakdown as $row)
                            <tr>
                                <td>{{ $row->district }}</td>
                                <td>{{ number_format($row->total_units, 2) }}</td>
                                <td>{{ number_format($row->total_hours, 1) }}</td>
                                <td>{{ number_format($row->total_water) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card chart-card mb-4">
        <div class="chart-card-header">
            <span class="icon-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </span>
            Farmers in {{ $state }}
        </div>
        <div class="chart-card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Application No.</th>
                            <th>Farmer Name</th>
                            <th>District</th>
                            <th>Capacity (HP)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($farmers as $farmer)
                            <tr>
                                <td>{{ $farmer->application_no }}</td>
                                <td>{{ $farmer->farmer_name }}</td>
                                <td>{{ $farmer->district }}</td>
                                <td>{{ rtrim(rtrim(number_format($farmer->pump_capacity_hp, 1), '0'), '.') }} HP</td>
                                <td>
                                    <span class="badge badge-{{ str_replace(' ', '_', $farmer->status) }}">
                                        {{ $farmer->status }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('generation.farmer', $farmer) }}" class="btn btn-sm btn-outline-success">Generation</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No farmers found in this state.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const trendLabels = @json($trendLabels);
        const trendUnits = @json($trendUnits);
        const stateTrendCtx = document.getElementById('stateTrendChart').getContext('2d');
        const stateTrendGradient = stateTrendCtx.createLinearGradient(0, 0, 0, 200);
        stateTrendGradient.addColorStop(0, 'rgba(47,111,224,0.30)');
        stateTrendGradient.addColorStop(1, 'rgba(139,92,246,0.03)');

        new Chart(stateTrendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Total Generation (kWh)',
                    data: trendUnits,
                    borderColor: '#2F6FE0',
                    backgroundColor: stateTrendGradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: trendLabels.length > 45 ? 0 : 3,
                    pointBackgroundColor: '#2F6FE0',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#EEF2F1' }, title: { display: true, text: 'kWh' } }
                }
            }
        });
    </script>

@endsection
