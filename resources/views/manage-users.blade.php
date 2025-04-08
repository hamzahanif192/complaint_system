@extends('main')
@section('dynamic_page')

<!-- TAB 1 -->
<div class="tab-pane active" id="tab-1" role="tabpanel">
    <h4>Other Department Users</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Department</th><th>Role</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users_general as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->department ? $user->department->name : 'N/A' }}</td>

                    <td>{{ $user->role }}</td>

                    <td>
                        <form method="POST" action="{{ url('/users/'.$user->id.'/update') }}">
                            @csrf
                            <select name="role">
                                <option value="department_head" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="employee" {{ $user->role == 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="department_head" {{ $user->role == 'department_head' ? 'selected' : '' }}>Department Head</option>
                            </select>
                            <button class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- TAB 2 -->
<div class="tab-pane" id="tab-2" role="tabpanel">
    <h4>Electrical, Mechanical, Plumbing, IT</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Department</th><th>Role</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users_special as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->department ? $user->department->name : 'N/A' }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <form method="POST" action="{{ url('/users/'.$user->id.'/update') }}">
                            @csrf
                            <select name="role">
                                <option value="employee" {{ $user->role == 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="department_head" {{ $user->role == 'department_head' ? 'selected' : '' }}>Department Head</option>
                            </select>
                            <button class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
