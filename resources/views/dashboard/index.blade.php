@extends('layouts.app')

@section('title', 'All States Dashboard - KLK Ventures')

@section('content')

    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h4 class="mb-0">All States Overview</h4>
            <div class="subtext">Consolidated PM-KUSUM performance across every state</div>
        </div>
    </div>

    <form method="GET" action="{{ route('dashboard.index') }}" class="row g-2 mb-3">
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
            <div class="stat-card b-teal">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['total_states'] }}</div>
                <div class="stat-label">States Covered</div>
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
                <div class="stat-label">Installed Pumps</div>
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
                <div class="stat-label">Total Generation (kWh)</div>
                <div class="stat-sub">last {{ $days }} {{ $days == 1 ? 'day' : 'days' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card chart-card mb-4">
                <div class="chart-card-header">
                    <span class="icon-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    </span>
                    Farmers by State
                </div>
                <div class="chart-card-body">
                    <canvas id="farmerStateChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card chart-card mb-4">
                <div class="chart-card-header">
                    <span class="icon-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </span>
                    Daily Total Generation Trend — last {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
                </div>
                <div class="chart-card-body">
                    <canvas id="trendChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card chart-card mb-4">
        <div class="chart-card-header">
            <span class="icon-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
            </span>
            State-wise Snapshot
        </div>
        <div class="chart-card-body">
            <div class="table-responsive">
                <table class="table table-bordered bg-white align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>State</th>
                            <th>Farmers</th>
                            <th>Generation ({{ $days }}d, kWh)</th>
                            <th>Water Discharge ({{ $days }}d, L)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($states as $stateName)
                            <tr>
                                <td>{{ $stateName }}</td>
                                <td>{{ $farmerCountByState[$stateName] ?? 0 }}</td>
                                <td>{{ number_format($generationByState[$stateName] ?? 0, 2) }}</td>
                                <td>{{ number_format($waterByState[$stateName] ?? 0) }}</td>
                                <td>
                                    <a href="{{ route('dashboard.state', $stateName) }}" class="btn btn-sm btn-outline-success">
                                        View {{ $stateName }} &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No states found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const stateLabels = @json($states);
        const farmerCounts = @json($states->map(fn($s) => $farmerCountByState[$s] ?? 0));
        const genValues = @json($states->map(fn($s) => round((float) ($generationByState[$s] ?? 0), 2)));

        new Chart(document.getElementById('farmerStateChart'), {
            type: 'doughnut',
            data: {
                labels: stateLabels,
                datasets: [{
                    data: farmerCounts,
                    backgroundColor: ['#2F6FE0', '#F97316', '#16A34A', '#06B6D4', '#EF4444'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, padding: 16, font: { family: 'IBM Plex Sans' } } } }
            }
        });

        const trendLabels = @json($trendLabels);
        const trendUnits = @json($trendUnits);
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 220);
        trendGradient.addColorStop(0, 'rgba(47,111,224,0.30)');
        trendGradient.addColorStop(1, 'rgba(139,92,246,0.03)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Total Generation (kWh)',
                    data: trendUnits,
                    borderColor: '#2F6FE0',
                    backgroundColor: trendGradient,
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
