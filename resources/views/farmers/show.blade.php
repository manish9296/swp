@extends('layouts.app')

@section('title', $farmer->farmer_name . ' - PM-KUSUM')

@section('content')
    <h4 class="mb-3">Farmer Details</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Application No.</dt>
                <dd class="col-sm-9">{{ $farmer->application_no }}</dd>

                <dt class="col-sm-3">IMEI Number</dt>
                <dd class="col-sm-9">{{ $farmer->imei_number }}</dd>

                <dt class="col-sm-3">Farmer Name</dt>
                <dd class="col-sm-9">{{ $farmer->farmer_name }}</dd>

                <dt class="col-sm-3">State</dt>
                <dd class="col-sm-9">{{ $farmer->state }}</dd>

                <dt class="col-sm-3">District</dt>
                <dd class="col-sm-9">{{ $farmer->district }}</dd>

                <dt class="col-sm-3">Pump Capacity</dt>
                <dd class="col-sm-9">{{ rtrim(rtrim(number_format($farmer->pump_capacity_hp, 1), '0'), '.') }} HP</dd>

                <dt class="col-sm-3">Component</dt>
                <dd class="col-sm-9">{{ $farmer->component }}</dd>

                <dt class="col-sm-3">Subsidy</dt>
                <dd class="col-sm-9">{{ number_format($farmer->subsidy_percent, 0) }}%</dd>

                <dt class="col-sm-3">Installation Date</dt>
                <dd class="col-sm-9">{{ $farmer->installation_date?->format('d-M-Y') ?? '-' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-{{ str_replace(' ', '_', $farmer->status) }}">
                        {{ $farmer->status }}
                    </span>
                </dd>
            </dl>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('generation.farmer', $farmer) }}" class="btn btn-success">View Generation Report</a>
        <a href="{{ route('farmers.edit', $farmer) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('farmers.index') }}" class="btn btn-outline-dark">Back to List</a>
    </div>
@endsection
