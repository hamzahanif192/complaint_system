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
        @if(isset($departments) && $departments->count())
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Department Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($departments as $dept)
                <tr>
                    <td>{{ $dept->id }}</td>
                    <td>{{ $dept->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif


    </div>
</div>
@endsection