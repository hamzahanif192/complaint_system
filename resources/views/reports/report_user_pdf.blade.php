<!DOCTYPE html>
<html>

<head>
    <title>User Complaint Report PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <h2>User Complaint Report</h2>

    <p><strong>Total Complaints:</strong> {{ $totalCount }}</p>
    <p><strong>Resolved Complaints:</strong> {{ $resolvedCount }}</p>
    <p><strong>Pending Complaints:</strong> {{ $pendingCount }}</p>
    <p><strong>Total Working Hours:</strong> {{ $totalWorkingHours }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Status</th>
                <th>Department</th>
                <th>Employee</th>
                <th>Assigned At</th>
                <th>Resolved At</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complaints as $c)
            @php
            $assigned = $c->trackings ? $c->trackings->firstWhere('action_type', 'assign_employee') : null;
            $resolved = $c->trackings ? $c->trackings->firstWhere(function ($t) {
            return $t->action_type === 'status_update' && str_contains($t->comment, 'Resolved');
            }) : null;

            $duration = ($assigned && $resolved)
            ? $resolved->created_at->diff($assigned->created_at)->format('%H:%I:%S')
            : '-';
            @endphp
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->complaint_type }}</td>
                <td>{{ $c->status }}</td>
                <td>{{ $c->assignedDepartment->name ?? '-' }}</td>
                <td>{{ $c->assignedEmployee->name ?? '-' }}</td>
                <td>{{ $assigned ? $assigned->created_at->format('d-m-Y H:i') : '-' }}</td>
                <td>{{ $resolved ? $resolved->created_at->format('d-m-Y H:i') : '-' }}</td>
                <td>{{ $duration }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>