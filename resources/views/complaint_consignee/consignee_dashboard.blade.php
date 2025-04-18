@extends('user_main')

@section('dynamic_page_user')
<div class="container mt-4">

  <h1>main karunga fix</h1>
  <h3 class="mb-4">Assigned Complaints (Resolve Section)</h3>

  @if($complaints->isEmpty())
  <div class="alert alert-info">No complaints assigned to you yet.</div>
  @else
  @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    ✅ {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if (session('error'))
  <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
    ⚠️ {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

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
        @if ($complaint->status == 'Pending')
                <span class="badge bg-danger">{{ $complaint->status }}</span>
                @elseif ($complaint->status == 'In Progress')
                <span class="badge bg-primary">{{ $complaint->status }}</span>
                @elseif ($complaint->status == 'Resolved')
                <span class="badge bg-success">{{ $complaint->status }}</span>
                @elseif ($complaint->status == 'On Hold')
                <span class="badge bg-warning">{{ $complaint->status }}</span>

                @elseif ($complaint->status == 'Cancelled')
                <span class="badge bg-secondary">{{ $complaint->status }}</span>
                @endif
        </td>
        <td>{{ optional($complaint->assignedDepartment)->name ?? 'N/A' }}</td>
        <td>{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
        <td>


          <!-- View Tracking Button -->
          <a href="#" data-bs-toggle="modal" data-bs-target="#trackingModal{{ $complaint->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye align-middle">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </a>

          <!-- Tracking Modal -->
          <div class="modal fade" id="trackingModal{{ $complaint->id }}" tabindex="-1"
            aria-labelledby="trackingModalLabel{{ $complaint->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="trackingModalLabel{{ $complaint->id }}">Complaint Tracking - ID
                    #{{ $complaint->id }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="complaintDetails">
                    <div class="row">
                      <div class="col-md-6">
                        <p><span><b>Created Date</b></span>: {{ $complaint->updated_at }}</p>
                      </div>
                      <div class="col-md-6">
                        <p style="float:right; text-align:right;">
                          @if ($complaint->status == 'Pending')
                          <span class="badge bg-danger">{{ $complaint->status }}</span>
                          @elseif ($complaint->status == 'In Progress')
                          <span class="badge bg-primary">{{ $complaint->status }}</span>
                          @elseif ($complaint->status == 'Resolved')
                          <span class="badge bg-success">{{ $complaint->status }}</span>
                          @elseif ($complaint->status == 'On Hold')
                          <span class="badge bg-warning">{{ $complaint->status }}</span>
                          @elseif ($complaint->status == 'Cancelled')
                          <span class="badge bg-secondary">{{ $complaint->status }}</span>
                          @endif
                        </p>



                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <p><span><b>User Name</b></span><br>{{ $complaint->full_name }}</p>
                      </div>
                      <div class="col-md-3">
                        <p><span><b>Type</b></span><br>{{ $complaint->complaint_type }}</p>
                      </div>
                      <div class="col-md-3">
                        <p><span><b>Department</b></span><br>{{ $complaint->depart }}</p>
                      </div>
                      <div class="col-md-3">
                        <p><span><b>Location</b></span><br>{{ $complaint->location }}</p>
                      </div>
                      <div class="col-md-3">
                        <p><span><b>Extension</b></span><br>{{ $complaint->tel_extension }}</p>
                      </div>
                      <div class="col-md-3">
                        <p><span><b>Assign by Department</b></span> {{ $complaint->assignedDepartment ? $complaint->assignedDepartment->name : 'Not Assigned' }}</p>
                      </div>
                      <div class="col-md-3">
                        <p><span><b>Consignee</b></span><br>{{ $complaint->assignedEmployee ? $complaint->assignedEmployee->name : 'Not Assigned' }}</p>
                      </div>
                      <div class="col-md-12">
                        <p><span><b>Message</b></span><br>{{$complaint->complaint_message}}</p>
                      </div>
                      <div class="col-md-12 ">
                        <div class="mb-10">
                        @php
                        $disabled = in_array($complaint->status, ['Resolved', 'On Hold']) ? 'disabled' : '';
                        @endphp

                        <form method="POST" action="" class="d-inline">
                          @csrf
                          <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                          <input type="hidden" name="status" value="Resolved">
                          <button type="submit" class="btn bg-success" {{ $disabled }}>Resolved</button>
                        </form>

                        <form method="POST" action="" class="d-inline">
                          @csrf
                          <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                          <input type="hidden" name="status" value="On Hold">
                          <button type="submit" class="btn bg-warning text-white" {{ $disabled }}>On Hold</button>
                        </form>
                        </div>

                      </div>
                    </div>
                  </div>
                  <!-- Logged in user role -->
                  <p class="text-muted mb-3">Logged in as: <strong>{{ auth()->user()->name }}
                      ({{ ucfirst(auth()->user()->role) }})</strong></p>

                  <!-- Timeline Section -->
                  <div id="tracking-timeline-{{ $complaint->id }}">
                    @foreach($trackings->where('complaint_id', $complaint->id) as $track)
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
                    <textarea id="comment-box-{{ $complaint->id }}" class="form-control" rows="3"
                      placeholder="Add comment..."></textarea>
                    <button class="btn btn-primary mt-2 submit-comment-btn" data-id="{{ $complaint->id }}">Submit
                      Comment</button>
                  </div>

        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>
@endsection