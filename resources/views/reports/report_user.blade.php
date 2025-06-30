@extends('main')
@section('dynamic_page')

<style>
    .singleDepartment-complaint .deleteBtn,
    .singleDepartment-complaint .eidtBtn {
        display: none;
    }
</style>

<div class="card singleDepartment-complaint">
    <div class="card-header">
        <h5 class="card-title mb-0">User Reporting</h5>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('report.user') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Employee</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- All --</option>
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
                    <a href="{{ route('report.user.pdf', request()->query()) }}" class="btn btn-outline-secondary w-100">Download PDF</a>
                </div>
                <a href="{{ route('report.user') }}" class="">Reset Filter</a>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        <h6>Total Complaints</h6>
                        <p class="fs-4 mb-0">{{ $totalCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        <h6>Resolved Complaints</h6>
                        <p class="fs-4 mb-0">{{ $resolvedCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        <h6>Pending Complaints</h6>
                        <p class="fs-4 mb-0">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        <h6>Total Working Time</h6>
                        <p class="fs-4 mb-0">{{ $totalWorkingHours }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Complainer</th>
                        <th>Type</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Extension</th>
                        <th>Status</th>
                        <th>Assigned Dept</th>
                        <th>Last Update</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $single)
                    @include('partials.complaint_row', ['single' => $single, 'trackings' => $single->trackings ?? collect()])
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            $complaints = $query->paginate(10)->appends($request->all());

        </div>
    </div>
</div>

@endsection