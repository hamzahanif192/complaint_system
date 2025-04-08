@extends('main')
@section('dynamic_page')


<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Add New Department</h5>
    </div>
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ url('/add_department') }}">
            @csrf
            <div class="form-group">
                <label for="name">Department Name</label>
                <input type="text" class="form-control" name="name" placeholder="e.g. HR, IT">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-2">Add Department</button>
        </form>

    </div>
</div>
@endsection