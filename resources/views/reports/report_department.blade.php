@extends('main')
@section('dynamic_page')

<div class="card singleDepartment-complaint">
    <div class="card-header">
        <h5 class="card-title mb-0">Complaints for {{ $department->name }}</h5>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('complaint.department', ['id' => $department->id]) }}" class="mb-4">
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
                    <a href="{{ route('complaint.department', ['id' => $department->id]) }}" class="btn btn-outline-secondary w-100">Reset Filter</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Complaint Type</th>
                        <th>Status</th>
                        <th>Assigned Employee</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $single)
                    <tr id="complaint-row-{{ $single->id }}">
                        <td>{{ $single->id }}</td>
                        <td>{{ $single->complaint_type }}</td>
                        <td>{{ $single->status }}</td>
                        <td>{{ $single->assignedEmployee->name ?? 'Not Assigned' }}</td>
                        <td>{{ $single->created_at }}</td>
                        <td>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $complaints->appends(request()->query())->links() }}  <!-- Pagination links -->
        </div>
    </div>
</div>

@endsection
