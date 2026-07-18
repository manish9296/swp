@php
    $farmer = $farmer ?? null;
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Application No. (RMS)</label>
        <input type="text" name="application_no" class="form-control"
               value="{{ old('application_no', $farmer->application_no ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">IMEI Number (RMS Device)</label>
        <input type="text" name="imei_number" class="form-control"
               value="{{ old('imei_number', $farmer->imei_number ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Farmer Name</label>
        <input type="text" name="farmer_name" class="form-control"
               value="{{ old('farmer_name', $farmer->farmer_name ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">State</label>
        <select name="state" class="form-select" required>
            <option value="">-- Select State --</option>
            @foreach($states as $state)
                <option value="{{ $state }}" @selected(old('state', $farmer->state ?? '') == $state)>{{ $state }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">District</label>
        <input type="text" name="district" class="form-control"
               value="{{ old('district', $farmer->district ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Pump Capacity (HP)</label>
        <select name="pump_capacity_hp" class="form-select" required>
            <option value="">-- Select Capacity --</option>
            @foreach([2, 3, 5, 7.5, 10] as $hp)
                <option value="{{ $hp }}" @selected((float) old('pump_capacity_hp', $farmer->pump_capacity_hp ?? '') == $hp)>{{ $hp }} HP</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Component</label>
        <select name="component" class="form-select" required>
            @foreach($components as $component)
                <option value="{{ $component }}" @selected(old('component', $farmer->component ?? '') == $component)>{{ $component }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Subsidy (%)</label>
        <input type="number" step="1" min="0" max="100" name="subsidy_percent" class="form-control"
               value="{{ old('subsidy_percent', $farmer->subsidy_percent ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Installation Date</label>
        <input type="date" name="installation_date" class="form-control"
               value="{{ old('installation_date', optional($farmer->installation_date ?? null)->format('Y-m-d')) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $farmer->status ?? '') == $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
</div>
