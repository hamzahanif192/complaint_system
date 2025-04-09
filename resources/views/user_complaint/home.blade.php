@extends('user_main')
@section('dynamic_page_user')
<div class="container">
    <h1>Main hun complaint karne wala </h1>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Add New Complaint</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" >All View Complaint</button>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif</div>
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <form action="{{ route('employee.complaint.submit') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Name -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="full_name"  placeholder="Enter your name" value="{{ auth()->user()->name }}">
                            {{-- @error('full_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror --}}
                        </div>
            
                        <!-- Department -->
                        <div class="form-group">
                            <label class="mb-0 mt-4 formLabel" for="department">Department</label>

                            @php
                            $department = array('--Select--','Admin','HR','Accounts','Procurement','Warehouse','Execution','Offset','Stitching','Sublimation','DTF','Designing','CTP','Tissue','Used Clothing');   
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
                            <input type="text" class="form-control" id="extension" name="tel_extension" placeholder="Enter your extension number">
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
                            $complaintType = array('--Select--', 'Plumbing','Electrical','Machine Movement','Construction Work','Maint. Dept.');
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
                                $location = array('--Select--','Office (Ground F)','Ground Floor','1st Floor','2nd Floor','Rooftop');
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
                            <textarea rows="5" type="text" class="form-control" id="complaint-message" name="complaint_message" placeholder="Enter your Complaint Here"></textarea>
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