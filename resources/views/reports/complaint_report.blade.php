@extends('main')
@section('dynamic_page')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Department Complaints Overview</h5>
    </div>
    <div class="card-body">
        
        <div class="boxWrapper-filter mt-2">
            <div class="row mb-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Total Complaints</h5>
                            <p class="mb-0 fs-4">{{ $totalComplaints }}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Resolved Complaints</h5>
                            <p class="mb-0 fs-4">{{ $resolvedComplaints }}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <!-- <div class="card"> -->
                        <!-- <div class="card-body text-center"> -->
                            <!-- <h5>Avg. Resolution Time</h5> -->
                            <!-- <p class="mb-0 fs-5">{{-- $averageResolutionTime --}}</p> -->
                        <!-- </div> -->
                    <!-- </div> -->
                </div>
            </div>
        </div>

        <h4 class="mt-4 treyin">Department-wise Report</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Queue</th>
                    <th>Resolved</th>
                    <th>Total</th>
                    <th>% Resolved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departmentReport as $row)
                <tr>
                    <td>{{ $row['department'] }}</td>
                    <td>{{ $row['queue'] }}</td>
                    <td>{{ $row['resolved'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['percent'] }}%</td>
                    <td>
                        <a href="{{ route('complaint.department', ['id' => $row['department_id']]) }}" class="btn btn-sm btn-info">
                            View All Complaints
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection