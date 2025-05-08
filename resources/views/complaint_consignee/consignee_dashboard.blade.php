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
  <div id="complaintsTableContainer">
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
        @foreach($complaints as $single)
        @include('partials.complaint_row', ['single' => $single, 'trackings' => $trackings])
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection