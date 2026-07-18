@extends('layouts.app')

@section('title', 'Add Farmer - PM-KUSUM')

@section('content')
    <h4 class="mb-3">Add New Farmer / Pump Record</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farmers.store') }}">
                @csrf
                @include('farmers._form')
                <button type="submit" class="btn btn-success">Save Record</button>
                <a href="{{ route('farmers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
