@extends('main')
@section('dynamic_page')
<div class="content">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Your Complaint</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Name -->
                            <div class="form-group">
                                <label class="mb-0 mt-4 formLabel" for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="full_name" placeholder="Enter your name" value="{{$complaint_details->full_name}}">
                                @error('full_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="form-group">
                                <label class="mb-0 mt-4 formLabel" for="department">Department</label>

                                @php
                                $department = array('--Select--','Admin','HR','Accounts','Procurement','Warehouse','Execution','Offset','Stitching','Sublimation','DTF','Designing','CTP','Tissue','Used Clothing');
                                @endphp
                                <select class="form-control" id="department" name="depart">
                                    @foreach ($department as $single)
                                    @if ($single == $complaint_details->depart )
                                    <option value="{{ $single }}" selected>{{ $single }}</option>
                                    @else
                                    <option value="{{ $single }}">{{ $single }}</option>
                                    @endif


                                    @endforeach

                                </select>
                                @error('depart')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Extension -->
                            <div class="form-group">
                                <label class="mb-0 mt-4 formLabel" for="extension">Extension</label>
                                <input type="text" class="form-control" id="extension" name="tel_extension" placeholder="Enter your extension number" value="{{$complaint_details->tel_extension}}">
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
                                    @if ($complaintTypeSingle == $complaint_details->complaint_type )
                                    <option value="{{ $complaintTypeSingle }}" selected>{{ $complaintTypeSingle }}</option>
                                    @else
                                    <option value="{{ $complaintTypeSingle }}">{{ $complaintTypeSingle }}</option>
                                    @endif
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
                                    @if ($item == $complaint_details->location )
                                    <option value="{{ $item }}" selected>{{ $item }}</option>
                                    @else
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endif
                                    @endforeach

                                </select>
                            </div>

                            <!-- Complaint Message -->
                            <div class="form-group">
                                <label class="mb-0 mt-4 formLabel" for="complaint-message">Complaint Message</label>
                                <textarea rows="5" type="text" class="form-control" id="complaint-message" name="complaint_message" placeholder="Enter your Complaint Here">{{$complaint_details->complaint_message}}</textarea>
                                @error('complaint_message')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row m-3">
                        <div class="col-md-4">
                            <label for="status">Status</label>
                            <select name="status" class="form-control form-select">
                                <option value="Pending" {{ $complaint_details->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ $complaint_details->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Resolved" {{ $complaint_details->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="On Hold" {{ $complaint_details->status == 'On Hold' ? 'selected' : '' }}>On Hold </option>
                                <option value="Cancelled" {{ $complaint_details->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="Department">Assign Department</label>
                            <select id="assigned_department_id" name="assigned_department_id" class="form-control form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('assigned_department_id', $complaint->assigned_department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="Employee">Employee</label>
                            <select id="assigned_employee_id" name="assigned_employee_id" class="form-control form-select">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    data-dept="{{ $emp->department_id }}"
                                    @if($emp->is_master) data-master="1" @endif
                                    {{ old('assigned_employee_id', $complaint->assigned_employee_id) == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary" name="submit">Update Complaint</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<script>
    const employeeSelect = document.getElementById('assigned_employee_id');
    const allOptions = Array.from(employeeSelect.options);

    document.getElementById('assigned_department_id').addEventListener('change', function() {
        const selectedDeptId = this.value;
        employeeSelect.innerHTML = '';

        const filtered = allOptions.filter(opt =>
            opt.value === '' || // Keep default
            opt.dataset.dept === selectedDeptId || // Match department
            opt.dataset.master === "1" // Include master
        );

        filtered.forEach(opt => employeeSelect.appendChild(opt));
    });

    document.getElementById('assigned_department_id').dispatchEvent(new Event('change'));
</script>
@endsection