@extends('main') 
@section('dynamic_page')

<div class="content">
<div class="row">
  <div class="col-12 col-lg-12 col-xxl-12 d-flex">
    <div class="card flex-fill">
      <div class="card-header">
        <h5 class="card-title mb-0">All Complaints</h5>
      </div>
      <table class="table table-hover my-0">
        <thead>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Complaint Type</th>
            <th>Department</th>
            <th>Location</th>
            <th>Ext</th>
            <th>Status</th>
            <th>Assignee</th>
            <th>Date/Time</th>
            @if(auth()->user()->isAdmin() || auth()->user()->isDepartmentHead())
              <th>Actions</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach ($complaint as $single)
            {{-- Department Head: skip if complaint not from their department --}}
            @if(auth()->user()->isDepartmentHead() && optional($single->assignedDepartment)->id !== auth()->user()->department_id)
              @continue
            @endif

            {{-- Employee: skip if not assigned --}}
            @if(auth()->user()->isEmployee() && auth()->user()->id !== $single->user_id)
              @continue
            @endif

            <tr>
              <td>{{ $single->id }}</td>
              <td>{{ $single->full_name }}</td>
              <td>{{ $single->complaint_type }}</td>
              <td>{{ $single->depart }}</td>
              <td>{{ $single->location }}</td>
              <td>{{ $single->tel_extension }}</td>
              <td>
                @if ($single->status == 'Pending')
                  <span class="badge bg-danger">{{ $single->status }}</span> 
                @elseif  ($single->status == 'In Progress')
                  <span class="badge bg-primary">{{ $single->status }}</span> 
                @elseif  ($single->status == 'Resolved')
                  <span class="badge bg-success">{{ $single->status }}</span> 
                @endif  
              </td>
              <td>{{ $single->assignedDepartment ? $single->assignedDepartment->name : 'Not Assigned' }}</td>
              <td>{{ $single->updated_at }}</td>

              @if(auth()->user()->isAdmin() || auth()->user()->isDepartmentHead())
              <td class="table-action">
                <a href="{{ url('/complaints/edit-complaint/' . $single->id)}}" class="eidtBtn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                </a>
                  <a href="{{ url('/complaints/delete-complaint/' . $single->id)}}" onclick="return confirm('Are you sure?')" class="deleteBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  </a>

                 <!-- View Tracking Button -->
<a href="#" data-bs-toggle="modal" data-bs-target="#trackingModal{{ $single->id }}">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye align-middle">
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
    <circle cx="12" cy="12" r="3"></circle>
  </svg>
</a>

<!-- Tracking Modal -->
<div class="modal fade" id="trackingModal{{ $single->id }}" tabindex="-1" aria-labelledby="trackingModalLabel{{ $single->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="trackingModalLabel{{ $single->id }}">Complaint Tracking - ID #{{ $single->id }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Logged in user role -->
        <p class="text-muted mb-3">Logged in as: <strong>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</strong></p>

        <!-- Timeline Section -->
        <div id="tracking-timeline-{{ $single->id }}">
          @foreach($trackings->where('complaint_id', $single->id) as $track)
            <div class="mb-2">
              <small>[ {{ \Carbon\Carbon::parse($track->created_at)->format('d-M h:i A') }} ]</small>
              <span>──●──</span>
              <strong>{{ ucfirst($track->action_type) }}</strong>
              @if($track->comment)
                <p class="mb-0">🗨️ <strong>{{ $track->performed_by }}</strong>: {{ $track->comment }}</p>
              @endif
            </div>
          @endforeach
        </div>

        <!-- Comment Form -->
        <div class="mt-3">
          <textarea id="comment-box-{{ $single->id }}" class="form-control" rows="3" placeholder="Add comment..."></textarea>
          <button class="btn btn-primary mt-2 submit-comment-btn" data-id="{{ $single->id }}">Submit Comment</button>
        </div>

      </div>
    </div>
  </div>
</div>


                      </div>
                    </div>
                  </div>
                </div>
              </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>




@endsection
