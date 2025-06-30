
<!DOCTYPE html>
<html>
<head>
    <title>Department Complaint Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; }
        .summary h4 { margin-bottom: 10px; }
        .filter-info { margin-top: 10px; font-size: 11px; }
    </style>
</head>
<body>

<h2>Department Complaint Report</h2>

<div class="summary">
    <h4>Summary</h4>
    <p>Total Complaints: {{ $total }}</p>
    <p>Resolved Complaints: {{ $resolved }}</p>
    <p>Queue Complaints: {{ $queue }}</p>
    <p>Average Resolution Time: {{ $averageResolutionTime }}</p>
</div>

<div class="filter-info">
    <strong>Filters Applied:</strong>
    <p>Status: {{ request('status') ?? 'All' }}</p>
    <p>Date Range: {{ request('start_date') ?? '-' }} to {{ request('end_date') ?? '-' }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Complainer</th>
            <th>Type</th>
            <th>Department</th>
            <th>Assigned Dept</th>
            <th>Status</th>
            <th>Created</th>
            <th>Resolved</th>
        </tr>
    </thead>
    <tbody>
        @foreach($complaints as $complaint)
        <tr>
            <td>{{ $complaint->id }}</td>
            <td>{{ $complaint->full_name }}</td>
            <td>{{ $complaint->complaint_type }}</td>
            <td>{{ $complaint->depart }}</td>
            <td>{{ $complaint->assignedDepartment->name ?? 'N/A' }}</td>
            <td>{{ $complaint->status }}</td>
            <td>{{ $complaint->created_at->format('d-m-Y') }}</td>
            <td>
                @php
                    $resolved = $trackings->where('complaint_id', $complaint->id)
                        ->where('action_type', 'status_update')
                        ->firstWhere('comment', 'like', '%Resolved%');
                @endphp
                {{ $resolved ? \Carbon\Carbon::parse($resolved->created_at)->format('d-m-Y') : 'N/A' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
