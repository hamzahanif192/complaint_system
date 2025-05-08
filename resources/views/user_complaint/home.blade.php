@extends('user_main')
@section('dynamic_page_user')
<div class="container">
    <h1>Main hun complaint karne wala </h1>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
                type="button" role="tab" aria-controls="pills-home" aria-selected="true">Add New Complaint</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
                type="button" role="tab" aria-controls="pills-profile" aria-selected="false">All View Complaint</button>
        </li>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

            @if($complaints->isEmpty())

            <p>You have not submitted any complaints.</p>
            @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Complaint Type</th>
                        <th>Location</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $single)
                    <tr>
                        <td>{{ $single->complaint_type }}</td>
                        <td>{{ $single->location }}</td>
                        <td>{{ $single->complaint_message }}</td>
                        <td>{{ $single->status }}</td>
                        <td>{{ $single->assignedEmployee ? $single->assignedEmployee->name : 'Not Assigned' }}</td>
                        <td>{{ $single->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            @php
                            $badgeColor = [
                            'Pending' => 'danger',
                            'In Progress' => 'primary',
                            'Resolved' => 'success',
                            'Cancelled' => 'secondary',
                            'Reopened' => 'info',
                            'On Hold' => 'warning',
                            ];
                            @endphp
                            <span
                                class="badge bg-{{ $badgeColor[$single->status] ?? 'dark' }}">{{ $single->status }}</span>

                        </td>
                        <td>{{ optional($single->assignedDepartment)->name ?? 'N/A' }}</td>
                        <td>{{ $single->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <!-- </a> -->
                            <!-- <a href="{{ url('/complaints/delete-complaint/' . $single->id)}}"
                                            onclick="return confirm('Are you sure?')" class="deleteBtn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-trash align-middle">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                </path>
                                            </svg>
                                        </a> -->

                            <!-- View Tracking Button -->
                            <a href="#" data-bs-toggle="modal" data-bs-target="#trackingModal{{ $single->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-eye align-middle">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>

                            <!-- Tracking Modal -->
                            <div class="modal fade" id="trackingModal{{ $single->id }}" tabindex="-1"
                                aria-labelledby="trackingModalLabel{{ $single->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="trackingModalLabel{{ $single->id }}">
                                                Complaint Tracking - ID
                                                #{{ $single->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="complaintDetails">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><span><b>Created Date</b></span>: {{ $single->updated_at }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p style="float:right; text-align:right;">
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
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <p><span><b>User Name</b></span><br>{{ $single->full_name }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><span><b>Type</b></span><br>{{ $single->single_type }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><span><b>Department</b></span><br>{{ $single->depart }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><span><b>Location</b></span><br>{{ $single->location }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><span><b>Extension</b></span><br>{{ $single->tel_extension }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><span><b>Assign by Department</b></span> {{ $single->assignedDepartment ? $single->assignedDepartment->name : 'Not Assigned' }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><span><b>Consignee</b></span><br>{{ $single->assignedEmployee ? $single->assignedEmployee->name : 'Not Assigned' }}</p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <p><span><b>Message</b></span><br>{{$single->single_message}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Logged in user role -->
                                            <p class="text-muted mb-3">Logged in as: <strong>{{ auth()->user()->name }}
                                                    ({{ ucfirst(auth()->user()->role) }})</strong></p>

                                            <!-- Timeline Section -->
                                            <div id="tracking-timeline-{{ $single->id }}">
                                                @foreach($trackings->where('single_id', $single->id) as $track)
                                                <div class="mb-2">
                                                    <small>[
                                                        {{ \Carbon\Carbon::parse($track->created_at)->format('d-M h:i A') }}
                                                        ]</small>
                                                    <span>──●──</span>
                                                    <strong>{{ ucfirst($track->action_type) }}</strong>
                                                    @if($track->comment)
                                                    <p class="mb-0">🗨️ <strong>{{ $track->performed_by }}</strong>:
                                                        {{ $track->comment }}
                                                    </p>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>

                                            <!-- Comment Form -->
                                            <div class="mt-3">
                                                <textarea id="comment-box-{{ $single->id }}" class="form-control"
                                                    rows="3" placeholder="Add comment..."></textarea>
                                                <button class="btn btn-primary mt-2 submit-comment-btn"
                                                    data-id="{{ $single->id }}">Submit
                                                    Comment</button>
                                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                ⚠️ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('employee.complaint.submit') }}" method="POST" id="complaintForm">
                @csrf
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Name -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="full_name"
                                placeholder="Enter your name" value="{{ auth()->user()->name }}">
                            {{-- @error('full_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror --}}
                        </div>

                        <!-- Department -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="department">Department</label>

                            @php
                            $department = array('--Select--', 'Admin', 'HR', 'Accounts', 'Procurement', 'Warehouse', 'Execution', 'Offset', 'Stitching', 'Sublimation', 'DTF', 'Designing', 'CTP', 'Tissue', 'Used Clothing');
                            @endphp
                            <select class="form-control" id="department" name="depart">
                                @foreach ($department as $single)

                                <option value="{{ $single }}">{{ $single }}</option>
                                @endforeach

                            </select>
                            @error('depart')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Extension -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="extension">Extension</label>
                            <input type="text" class="form-control" id="extension" name="tel_extension"
                                placeholder="Enter your extension number">
                            @error('tel_extension')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Complaint Type -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="complaintType">Complaint Type</label>

                            @php
                            $complaintType = array('--Select--', 'Plumbing', 'Electrical', 'Machine Movement', 'Construction Work', 'Maint. Dept.');
                            @endphp
                            <select class="form-control" id="complaintType" name="complaint_type">

                                @foreach ($complaintType as $complaintTypeSingle)
                                <option value="{{ $complaintTypeSingle }}">{{ $complaintTypeSingle }}</option>

                                @endforeach

                            </select>
                        </div>

                        <!-- Location -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="location">Location</label>

                            @php
                            $location = array('--Select--', 'Office (Ground F)', 'Ground Floor', '1st Floor', '2nd Floor', 'Rooftop');
                            @endphp
                            <select class="form-control" id="location" name="location">
                                @foreach ($location as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach

                            </select>
                        </div>

                        <!-- Complaint Message -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="complaint-message">Complaint Message</label>
                            <textarea rows="5" type="text" class="form-control" id="complaint-message"
                                name="complaint_message" placeholder="Enter your Complaint Here"></textarea>
                            @error('complaint_message')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary" name="submit">Submit Complaint</button>
                </div>
            </form>
        </div>



    </div>
</div>

@endsection