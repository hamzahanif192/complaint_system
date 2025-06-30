@extends('main')
@section('dynamic_page')

<div class="content">
  <div class="row">
    <div class="col-12 col-lg-12 col-xxl-12 d-flex">
      <div class="card flex-fill">
        <div class="card-header">
          <h5 class="card-title mb-0">All Complaints</h5>
        </div>
        <div id="complaintsTableContainer">
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
              @include('partials.complaint_row', ['single' => $single, 'trackings' => $trackings])
              @endforeach
            </tbody>

          </table>

        </div>
      </div>
    </div>
    @endsection