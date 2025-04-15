@extends('main')
@section('dynamic_page')

    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">

                {{-- 🔴 Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>⚠️ Please fix the following error(s):</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ✅ Success Alert --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ {{ session('success') }}
        @if (session('user_created'))
            <br>
            Email: <strong>{{ session('user_created') }}</strong> — Check if this was intentional.
        @endif
        <a href="{{ url('/users') }}" class="btn btn-sm btn-outline-light float-end">View All Users</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


                    <div class="d-block align-middle">
                        <div class="text-center mt-4">
                            <h1 class="h2">Create an Account</h1>
                            <p class="lead">Join the system as an Employee</p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url('/signup') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        <select class="form-control" name="department_id" required>
                                            <option value="">-- Select Department --</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            Already have an account? <a href="{{ url('/login') }}">Log In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection