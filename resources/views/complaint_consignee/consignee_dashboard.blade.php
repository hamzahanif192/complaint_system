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
    <span
    class="badge bg-{{ $complaint->status == 'Resolved' ? 'success' : ($complaint->status == 'In Progress' ? 'primary' : 'danger') }}">
    {{ $complaint->status }}
    </span>
    </td>
    <td>{{ optional($complaint->assignedDepartment)->name ?? 'N/A' }}</td>
    <td>{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
    <td>
    </a>
    <a href="{{ url('/complaints/delete-complaint/' . $complaint->id)}}" onclick="return confirm('Are you sure?')"
    class="deleteBtn">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      class="feather feather-trash align-middle">
      <polyline points="3 6 5 6 21 6"></polyline>
      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
    </svg>
    </a>

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