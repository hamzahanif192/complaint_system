@extends('user_main')

@section('dynamic_page_user')
<div class="container mt-4">
    
<h1>main karunga fix</h1>
    <h3 class="mb-4">Assigned Complaints (Resolve Section)</h3>

    @if($complaints->isEmpty())
        <div class="alert alert-info">No complaints assigned to you yet.</div>
    @else
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Type</th>
                <th>Location</th>
                <th>Message</th>
                <th>Status</th>
                <th>Assigned By Dept</th>
                <th>Assigned At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complaints as $complaint)
            <tr>
                <td>{{ $complaint->id }}</td>
                <td>{{ $complaint->complaint_type }}</td>
                <td>{{ $complaint->location }}</td>
                <td>{{ $complaint->complaint_message }}</td>
                <td>
                    <span class="badge bg-{{ $complaint->status == 'Resolved' ? 'success' : ($complaint->status == 'In Progress' ? 'primary' : 'danger') }}">
                        {{ $complaint->status }}
                    </span>
                </td>
                <td>{{ optional($complaint->assignedDepartment)->name ?? 'N/A' }}</td>
                <td>{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
