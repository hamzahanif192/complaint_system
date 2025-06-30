
@extends('main')
@section('dynamic_page')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">User Resolver Complaint Report</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('report.user') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label for="user_id">Employee</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- All Employees --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    <a href="{{ route('report.user') }}" class="btn btn-outline-danger w-100">Reset</a>
                </div>
            </div>
        </form>

        <!-- Summary and table would go below -->
    </div>
</div>
@endsection
