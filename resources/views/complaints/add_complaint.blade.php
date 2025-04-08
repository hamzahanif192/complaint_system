@extends('main')
@section('dynamic_page')
<div class="content">
<div class="row">
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Add Your Complaint</h5>
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
                        <input type="text" class="form-control" id="name" name="full_name" placeholder="Enter your name">
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
  </div>
@endsection