@extends('layouts.app')

@section('title', 'Edit Farmer - PM-KUSUM')

@section('content')
    <h4 class="mb-3">Edit Farmer / Pump Record</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farmers.update', $farmer) }}">
                @csrf
                @method('PUT')
                @include('farmers._form')
                <button type="submit" class="btn btn-success">Update Record</button>
                <a href="{{ route('farmers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
