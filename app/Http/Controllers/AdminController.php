<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplaintModel;
use App\Models\Department;
use PHPUnit\Framework\MockObject\ReturnValueNotConfiguredException;

class AdminController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('login');
    }

    // Handle login submission
    public function handleLogin(Request $req)
    {
        if ($req->has('submit')) {
            // die("Button pressed");                                                                       
            $req->validate([
                'email' => 'required',
                'password' => 'required'
            ]);

            if (Auth::attempt($req->only('email', 'password'))) {
                return redirect('/dashboard');
            } else {
                return redirect('/login')->withErrors('Incorrect Username or Password');
            }
        }
        return back()->with('error', 'Invalid request');
    }
    // dashboard
    public function dashboard()
    {
        return view('dashboard');
    }
    // logout
    public function logout()
    {
        \Session::flush();
        Auth::logout();
        return redirect('/login');
    }

    // add complaint
    public function add_complaint()
    {
        return view('complaints.add_complaint');
    }
    public function add_complaint_req(Request $req)
    {
        if ($req->has('submit')) {

            $req->validate([
                'full_name' => 'required',
                'depart' => 'required',
                'tel_extension' => 'required',
                'complaint_message' => 'required',
            ]);
            $complaint = new ComplaintModel;
            $complaint->full_name = $req['full_name'];
            $complaint->depart = $req['depart'];
            $complaint->tel_extension = $req['tel_extension'];
            $complaint->complaint_type = $req['complaint_type'];
            $complaint->location = $req['location'];
            $complaint->complaint_message = $req['complaint_message'];
            $complaint->status = $req->status ?? 'Pending';

            $complaint->save();
            return redirect('complaints/view-complaint');
        }
        return back()->with('error', 'Invalid request');
    }
    // view complaint
    public function view_complaint()
{
    $user = auth()->user();

    if ($user->isAdmin()) {
        $complaints = ComplaintModel::all();
    } elseif ($user->isDepartmentHead()) {
        $complaints = ComplaintModel::where('assigned_department_id', $user->department_id)->get();
    } elseif ($user->isEmployee()) {
        $complaints = ComplaintModel::where('assigned_employee_id', $user->id)->get();
    } else {
        $complaints = collect(); // Empty collection if no role
    }

    return view('complaints.view_complaint', [
        'complaint' => $complaints,
        'user' => $user, // Pass the logged-in user to Blade
    ]);
}

    
    public function delete_complaint($id)
    {
        $complaint = ComplaintModel::find($id);

        if ($complaint == '') {
            return redirect('complaints/view-complaint');
        } else {
            $complaint->delete();
            return redirect('complaints/view-complaint');
            // return
        }
    }

    // edit_complaint

    public function edit_complaint($id, Request $req)
    {
        $complaint = ComplaintModel::find($id);
        if (!$complaint) {
            return redirect('complaints/view-complaint')->with('error', 'Complaint not found');
        }
    
        if ($req->isMethod('post')) {
            $req->validate([
                'full_name' => 'required',
                'depart' => 'required',
                'tel_extension' => 'required',
                'complaint_message' => 'required',
                'assigned_department_id' => 'nullable|exists:departments,id',
                'assigned_employee_id' => 'nullable|exists:users,id',
            ]);
    
            $complaint->full_name = $req->full_name;
            $complaint->depart = $req->depart;
            $complaint->tel_extension = $req->tel_extension;
            $complaint->complaint_type = $req->complaint_type;
            $complaint->location = $req->location;
            $complaint->complaint_message = $req->complaint_message;
            $complaint->status = $req->status ?? 'Pending';
            $complaint->assigned_department_id = $req->assigned_department_id;
            $complaint->assigned_employee_id = $req->assigned_employee_id;
    
            $complaint->save();
    
            return redirect('complaints/view-complaint')->with('success', 'Complaint updated successfully');
        }
    
        $departments = Department::all();
    
        // Only fetch employees for department head
        $employees = [];
        if (auth()->user()->isDepartmentHead()) {
            $employees = User::where('role', 'employee')
                ->where('department_id', auth()->user()->department_id)
                ->get();
        }
    
        return view('complaints.edit-complaint', compact('complaint', 'departments', 'employees'))
            ->with('complaint_details', $complaint);
    }
    
    
// public function assignedHead()
// {
//     return $this->belongsTo(User::class, 'assigned_to_head');
// }

public function manageUsers()
{
    $excludedDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];

    $excludedDepartmentIds = \App\Models\Department::whereIn('name', $excludedDepartments)->pluck('id')->toArray();

    $users_general = User::with('department')
    ->where(function ($query) use ($excludedDepartmentIds) {
        $query->whereNull('department_id')
              ->orWhereNotIn('department_id', $excludedDepartmentIds);
    })->get();

$users_special = User::with('department')
    ->whereIn('department_id', $excludedDepartmentIds)
    ->get();
    return view('manage-users', compact('users_general', 'users_special'));
}

    // Update User Role
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Validate request
        $request->validate([
            'role' => 'required|in:employee,department_head',
            'department_id' => 'nullable|exists:departments,id', // Ensure department exists
        ]);
    
        $user->role = $request->role;
    
        // Only update department_id if role is 'department_head'
        if ($request->role === 'department_head' && $request->filled('department_id')) {
            $user->department_id = $request->department_id;
        }
        
        // Don’t reset if user already has department assigned
        if ($request->role === 'employee' && $user->department_id === null && $request->filled('department_id')) {
            $user->department_id = $request->department_id;
        }
    
        $user->save();
    
        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function addDepartmentForm()
{
    return view('add_department'); // adjust path if different
}

public function storeDepartment(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:departments,name',
    ]);

    Department::create([
        'name' => $request->name,
    ]);

    return back()->with('success', 'Department added successfully!');
}
    
}
