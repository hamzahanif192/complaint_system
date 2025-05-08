@if(auth()->user()->isDepartmentHead() && optional($single->assignedDepartment)->id !== auth()->user()->department_id)
    @php return; @endphp
@endif

@if(auth()->user()->isEmployee() && auth()->user()->id !== $single->assigned_employee_id)
    @php return; @endphp
@endif


<tr id="complaint-row-{{ $single->id }}" class="complaint-row-{{ $single->id }}">

    <td>{{ $single->id }}</td>
    <td>{{ $single->full_name }}</td>
    <td>{{ $single->complaint_type }}</td>
    <td>{{ $single->depart }}</td>
    <td>{{ $single->location }}</td>
    <td>{{ $single->tel_extension }}</td>
    <td>
        @if ($single->status == 'Pending')
        <span class="badge bg-danger">{{ $single->status }}</span>
        @elseif ($single->status == 'In Progress')
        <span class="badge bg-primary">{{ $single->status }}</span>
        @elseif ($single->status == 'Resolved')
        <span class="badge bg-success">{{ $single->status }}</span>
        @elseif ($single->status == 'On Hold')
        <span class="badge bg-warning">{{ $single->status }}</span>
        @elseif ($single->status == 'Cancelled')
        <span class="badge bg-secondary">{{ $single->status }}</span>
        @endif
    </td>
    <td>{{ $single->assignedDepartment ? $single->assignedDepartment->name : 'Not Assigned' }}</td>
    <td>{{ $single->updated_at }}</td>

    <td class="table-action">
        @if(auth()->user()->isAdmin() || auth()->user()->isDepartmentHead())

        <a href="{{ url('/complaints/edit-complaint/' . $single->id)}}" class="eidtBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle">
                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
            </svg>
        </a>

        <a href="{{ url('/complaints/delete-complaint/' . $single->id)}}" onclick="return confirm('Are you sure?')" class="deleteBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        </a>
     
        @endif
        @if(isset($newlyAdded) && $newlyAdded)
            <button type="button" onclick="location.reload()" class="btn btn-sm btn-warning">Reload</button>
        @else
            <a href="#" data-bs-toggle="modal" data-bs-target="#trackingModal{{ $single->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye align-middle">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </a>
        @endif

        </td>
</tr>

{{-- Tracking Modal --}}
<div class="modal fade" id="trackingModal{{ $single->id }}" tabindex="-1" aria-labelledby="trackingModalLabel{{ $single->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackingModalLabel{{ $single->id }}">Complaint Tracking - ID #{{ $single->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="complaintDetails-wrapper">
                    <div class="row">
                        <div class="col-md-6">
                            <p><b>Created Date:</b> {{ $single->updated_at }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if ($single->status == 'Pending')
                            <span class="badge bg-danger">{{ $single->status }}</span>
                            @elseif ($single->status == 'In Progress')
                            <span class="badge bg-primary">{{ $single->status }}</span>
                            @elseif ($single->status == 'Resolved')
                            <span class="badge bg-success">{{ $single->status }}</span>
                            @elseif ($single->status == 'On Hold')
                            <span class="badge bg-warning">{{ $single->status }}</span>
                            @elseif ($single->status == 'Cancelled')
                            <span class="badge bg-secondary">{{ $single->status }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><b>User Name:</b> {{ $single->full_name }}</div>
                        <div class="col-md-3"><b>Type:</b> {{ $single->complaint_type }}</div>
                        <div class="col-md-3"><b>Department:</b> {{ $single->depart }}</div>
                        <div class="col-md-3"><b>Location:</b> {{ $single->location }}</div>
                        <div class="col-md-3"><b>Extension:</b> {{ $single->tel_extension }}</div>
                        <div class="col-md-3"><b>Assigned Department:</b> {{ $single->assignedDepartment ? $single->assignedDepartment->name : 'Not Assigned' }}</div>
                        <div class="col-md-3"><b>Assigned Employee:</b> {{ $single->assignedEmployee ? $single->assignedEmployee->name : 'Not Assigned' }}</div>
                    </div>
                    <div class="col-md-12"><b>Message:</b> {{ $single->complaint_message }}</div>
                </div>
                <hr>
                <div id="tracking-timeline-{{ $single->id }}">
                    @foreach($trackings->where('complaint_id', $single->id) as $track)
                    <div>
                        <small>{{ \Carbon\Carbon::parse($track->created_at)->format('d-M h:i A') }}</small> — 
                        <strong>{{ ucfirst($track->action_type) }}</strong>
                        @if($track->comment)
                        <p><strong>{{ $track->performed_by }}:</strong> {{ $track->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
