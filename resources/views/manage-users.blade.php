@extends('main')
@section('dynamic_page')
<!-- <h1>This is manage-users.blade.php</h1>
<pre>{{ print_r($departments ?? 'not passed', true) }}</pre> -->

    <!-- TAB 1 -->
    <div class="tab-pane active" id="tab-1" role="tabpanel">
        <h4>Other Department Users</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Actions</th>
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
                            <form method="POST" action="{{ url('/users/' . $user->id . '/update') }}">
                                @csrf
                                <select name="role">
                                    <option value="employee" {{ $user->role == 'employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="department_head" {{ $user->role == 'department_head' ? 'selected' : '' }}>
                                        Department Head</option>
                                </select>
                                <select name="department_id" class="form-select mt-2">
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Actions</th>
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
                           <form method="POST" action="{{ url('/users/' . $user->id . '/update') }}">
    @csrf
    <select name="role">
        <option value="employee" {{ $user->role == 'employee' ? 'selected' : '' }}>Employee</option>
        <option value="department_head" {{ $user->role == 'department_head' ? 'selected' : '' }}>
            Department Head
        </option>
    </select>

    <select name="department_id" class="form-select mt-2">
        <option value="">-- Select Department --</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
            </option>
        @endforeach
    </select>

    @if($user->role == 'employee')
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_master" id="master{{ $user->id }}"
                   {{ $user->is_master ? 'checked' : '' }}>
            <label class="form-check-label" for="master{{ $user->id }}">
                Master Resolver
            </label>
        </div>
    @endif

    <button class="btn btn-sm btn-primary mt-2">Update</button>
</form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection