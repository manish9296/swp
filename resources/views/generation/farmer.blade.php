@extends('layouts.app')

@section('title', 'RMS Generation Report - ' . $farmer->farmer_name)

@section('content')

    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h4 class="mb-0">{{ $farmer->farmer_name }}</h4>
            <div class="subtext">
                {{ $farmer->district }}, {{ $farmer->state }} &middot;
                {{ rtrim(rtrim(number_format($farmer->pump_capacity_hp, 1), '0'), '.') }} HP pump &middot;
                ~{{ $farmer->panelCapacityKw() }} kWp panel &middot;
                App No: <strong>{{ $farmer->application_no }}</strong> &middot;
                IMEI: <strong>{{ $farmer->imei_number }}</strong>
            </div>
        </div>
        <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-outline-dark btn-sm">&larr; Back to Farmer</a>
    </div>

    <form method="GET" action="{{ route('generation.farmer', $farmer) }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <label class="form-label mb-1">Show data for last:</label>
            <select name="days" class="form-select" onchange="this.form.submit()">
                @foreach([1, 7, 15, 30, 60, 90] as $opt)
                    <option value="{{ $opt }}" @selected($days == $opt)>{{ $opt }} {{ $opt == 1 ? 'day' : 'days' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label mb-1">Or enter custom range (1-90 days):</label>
            <div class="input-group">
                <input type="number" name="days" min="1" max="90" value="{{ $days }}" class="form-control">
                <button class="btn btn-success" type="submit">Update</button>
            </div>
        </div>
    </form>

    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="stat-card b-orange">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['total_units'] }}</div>
                <div class="stat-label">Total Generation (kWh)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-blue">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['avg_units'] }}</div>
                <div class="stat-label">Avg / Day (kWh)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-green">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $stats['peak_units'] }}</div>
                <div class="stat-label">Peak Day (kWh)</div>
                <div class="stat-sub">{{ $stats['peak_date'] ?? '-' }}</div>
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
            Daily Generation (kWh) — last {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
        </div>
        <div class="chart-card-body">
            <canvas id="generationChart" height="100"></canvas>
        </div>
    </div>

    <div class="card chart-card mb-4">
        <div class="chart-card-header">
            <span class="icon-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            Daily Pump Run Hours — last {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
        </div>
        <div class="chart-card-body">
            <canvas id="hoursChart" height="90"></canvas>
        </div>
    </div>

    {{-- RMS-style raw data table with CSV / Excel / PDF export --}}
    <div class="card chart-card mb-4">
        <div class="chart-card-header">
            <span class="icon-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
            </span>
            RMS Daily Data Log
        </div>
        <div class="chart-card-body">
            <div class="table-responsive">
                <table id="rmsTable" class="table table-bordered table-striped bg-white align-middle" style="width:100%">
                    <thead class="table-success">
                        <tr>
                            <th>Application No.</th>
                            <th>IMEI Number</th>
                            <th>Process Date</th>
                            <th>Time Stamp</th>
                            <th>RMS Data Status</th>
                            <th>DC Input Voltage (V)</th>
                            <th>Today Solar Generation (KWh)</th>
                            <th>Today Pump Day Run Hours (Hrs)</th>
                            <th>Today Water Discharge (Liter)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $r)
                            <tr>
                                <td>{{ $farmer->application_no }}</td>
                                <td>{{ $farmer->imei_number }}</td>
                                <td>{{ $r->process_date->format('d-m-Y') }}</td>
                                <td>{{ $r->time_stamp->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    <span class="badge {{ $r->rms_data_status === 'VALID' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $r->rms_data_status }}
                                    </span>
                                </td>
                                <td>{{ number_format($r->dc_input_voltage, 2) }}</td>
                                <td>{{ number_format($r->solar_generation_kwh, 2) }}</td>
                                <td>{{ number_format($r->pump_run_hours, 2) }}</td>
                                <td>{{ number_format($r->water_discharge_liter, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const chartLabels = @json($chartLabels);
        const units = @json($units);
        const hours = @json($hours);

        const genCtx = document.getElementById('generationChart').getContext('2d');
        const genGradient = genCtx.createLinearGradient(0, 0, 0, 200);
        genGradient.addColorStop(0, 'rgba(47,111,224,0.30)');
        genGradient.addColorStop(1, 'rgba(139,92,246,0.03)');

        new Chart(genCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Units Generated (kWh)',
                    data: units,
                    borderColor: '#2F6FE0',
                    backgroundColor: genGradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: chartLabels.length > 45 ? 0 : 3,
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

        new Chart(document.getElementById('hoursChart'), {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pump Run Hours',
                    data: hours,
                    backgroundColor: '#F97316',
                    borderRadius: 5,
                    maxBarThickness: 22,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#EEF2F1' }, title: { display: true, text: 'Hours' } }
                }
            }
        });
    </script>

    {{-- DataTables + Buttons (CSV / Excel / PDF export), matches RMS dashboard UX --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(function () {
            $('#rmsTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 90],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-sm btn-outline-secondary' },
                    { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-sm btn-outline-secondary' },
                    { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-sm btn-outline-secondary', orientation: 'landscape' },
                ],
            });
        });
    </script>

@endsection
