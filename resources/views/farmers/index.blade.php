@extends('layouts.app')

@section('title', 'Farmers List - PM-KUSUM')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h4 class="mb-0">Farmers &amp; Pump Records</h4>
            <div class="subtext">All PM-KUSUM applications across states</div>
        </div>
        <a href="{{ route('farmers.create') }}" class="btn btn-success">+ Add New Farmer</a>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="stat-card b-blue">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $summary['total'] }}</div>
                <div class="stat-label">Total Applications</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-green">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $summary['installed'] }}</div>
                <div class="stat-label">Installed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-yellow">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $summary['pending'] }}</div>
                <div class="stat-label">In Progress / Pending</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card b-teal">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $summary['by_state']->count() }}</div>
                <div class="stat-label">States Covered</div>
                </div>
            </div>
        </div>
    </div>


    <form method="GET" action="{{ route('farmers.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="state" class="form-select">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" @selected(request('state') == $state)>{{ $state }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="component" class="form-select">
                <option value="">All Components</option>
                @foreach($components as $component)
                    <option value="{{ $component }}" @selected(request('component') == $component)>{{ $component }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100" type="submit">Apply Filters</button>
        </div>
    </form>

    <div class="table-responsive shadow-sm">
        <table class="table table-bordered table-hover bg-white align-middle">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Application No.</th>
                    <th>IMEI Number</th>
                    <th>Farmer Name</th>
                    <th>State</th>
                    <th>District</th>
                    <th>Capacity (HP)</th>
                    <th>Component</th>
                    <th>Subsidy %</th>
                    <th>Installation Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($farmers as $farmer)
                    <tr>
                        <td>{{ $loop->iteration + ($farmers->currentPage() - 1) * $farmers->perPage() }}</td>
                        <td>{{ $farmer->application_no }}</td>
                        <td>{{ $farmer->imei_number }}</td>
                        <td>{{ $farmer->farmer_name }}</td>
                        <td>{{ $farmer->state }}</td>
                        <td>{{ $farmer->district }}</td>
                        <td>{{ rtrim(rtrim(number_format($farmer->pump_capacity_hp, 1), '0'), '.') }} HP</td>
                        <td>{{ $farmer->component }}</td>
                        <td>{{ number_format($farmer->subsidy_percent, 0) }}%</td>
                        <td>{{ $farmer->installation_date?->format('d-M-Y') ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ str_replace(' ', '_', $farmer->status) }}">
                                {{ $farmer->status }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('generation.farmer', $farmer) }}" class="btn btn-sm btn-outline-success">Generation</a>
                            <a href="{{ route('farmers.edit', $farmer) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('farmers.destroy', $farmer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $farmers->links() }}
    </div>

@endsection
