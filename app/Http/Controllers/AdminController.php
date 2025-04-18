<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplaintModel;
use App\Models\Department;
use App\Models\ComplaintTracking;
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
        $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($req->only('email', 'password'))) {
            $user = Auth::user();

            if ($user->isAdmin() || $user->isDepartmentHead()) {
                return redirect('/dashboard'); // Admin + Dept Head dashboard
            }

            if ($user->isEmployee()) {
                $resolverDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];
                $deptName = optional($user->department)->name;

                if (in_array($deptName, $resolverDepartments)) {
                    return redirect('/dashboard/employee/resolver'); // Resolver employee
                } else {
                    return redirect('/dashboard/employee'); // Complaint creator employee
                }
            }
        }


        return redirect('/login')->withErrors('Incorrect Username or Password');
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
            $complaints = collect();
        }

        $trackings = ComplaintTracking::all();

        return view('complaints.view_complaint', [
            'complaint' => $complaints,
            'trackings' => $trackings,
            'user' => $user,
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

            // Save old values before updating
            $originalDept = $complaint->assigned_department_id;
            $originalEmp = $complaint->assigned_employee_id;
            $originalStatus = $complaint->status;

            // Update
            $complaint->full_name = $req->full_name;
            $complaint->depart = $req->depart;
            $complaint->tel_extension = $req->tel_extension;
            $complaint->complaint_type = $req->complaint_type;
            $complaint->location = $req->location;
            $complaint->complaint_message = $req->complaint_message;
            $complaint->status = $req->status ?? 'Pending';
            $complaint->assigned_department_id = $req->assigned_department_id;
            $complaint->assigned_employee_id = $req->assigned_employee_id;

            if (auth()->user()->isDepartmentHead() && $complaint->status == 'Pending') {
                $complaint->status = 'In Progress';
            }

            $complaint->save();


            // Tracking entries
            if ($req->assigned_department_id && $req->assigned_department_id != $originalDept) {
                ComplaintTracking::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => auth()->id(),
                    'action_type' => 'assign_department',
                    'performed_by' => auth()->user()->name,
                    'comment' => 'Assigned to department',
                ]);
            }

            if ($req->assigned_employee_id && $req->assigned_employee_id != $originalEmp) {
                ComplaintTracking::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => auth()->id(),
                    'action_type' => 'assign_employee',
                    'performed_by' => auth()->user()->name,
                    'comment' => 'Assigned to employee',
                ]);
            }

            if ($req->status && $req->status != $originalStatus) {
                ComplaintTracking::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => auth()->id(),
                    'action_type' => 'status_update',
                    'performed_by' => auth()->user()->name,
                    'comment' => 'Status changed to ' . $req->status,
                ]);
            }
            

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
        $departments = Department::all();

        return view('manage-users', compact('users_general', 'users_special', 'departments'));
    }

    // Update User Role
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:employee,department_head',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user->role = $request->role;

        if ($request->filled('department_id')) {
            $user->department_id = $request->department_id;
        }

        $user->save();

        return redirect()->back()->with('success', 'User role and department updated successfully.');
    }


    public function addDepartmentForm()
    {

        $departments = Department::all();
        return view('add_department', compact('departments')); // adjust path if different
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

    public function employeeDashboard()
    {
        return view('user_complaint.home');
    }

    public function departmentHeadDashboard()
    {
        return view('complaint_consignee.consignee_dashboard');
    }
    public function employeeSubmitComplaint(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'depart' => 'required|string|max:255',
            'tel_extension' => 'required|string|max:10',
            'complaint_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'complaint_message' => 'required|string',
        ]);

        $complaint = new ComplaintModel();
        $complaint->user_id = auth()->id();
        $complaint->full_name = $request->full_name;
        $complaint->depart = $request->depart;
        $complaint->tel_extension = $request->tel_extension;
        $complaint->complaint_type = $request->complaint_type;
        $complaint->location = $request->location;
        $complaint->complaint_message = $request->complaint_message;
        $complaint->status = 'Pending';
        $complaint->save();

        return redirect()->back()->with('success', 'Complaint submitted successfully!');
    }
    public function employeeResolverDashboard()
    {
        return view('complaint_consignee.consignee_dashboard');
    }
    // public function employeeDashboard()
    public function employeeDashboardview()
    {
        $complaints = ComplaintModel::where('user_id', auth()->id())->get();
        $trackings = ComplaintTracking::all();
        return view('user_complaint.home', compact('complaints', 'trackings'));
    }
    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }
    public function employeeResolverView(Request $request)
    {
        if ($request->isMethod('post')) {
            $complaint = ComplaintModel::find($request->complaint_id);
    
            // Handle if complaint not found
            if (!$complaint) {
                return back()->with('error', 'Complaint not found.');
            }
    
            // Prevent duplicate update
            if (in_array($complaint->status, ['Resolved', 'On Hold'])) {
                return back()->with('error', 'Status already finalized.');
            }
    
            // Update status
            $complaint->status = $request->status;
            $complaint->save();
    
            // Track the update
            ComplaintTracking::create([
                'complaint_id' => $complaint->id,
                'user_id' => auth()->id(),
                'status' => $request->status,
                'action_type' => 'status_update',
                'comment' => 'Status updated by consignee.',
                'performed_by' => auth()->user()->name,
            ]);
    
            return back()->with('success', 'Status updated to ' . $request->status);
        }
    
        // Regular GET load
        $complaints = ComplaintModel::where('assigned_employee_id', auth()->id())->get();
        $trackings = ComplaintTracking::all();
    
        return view('complaint_consignee.consignee_dashboard', compact('complaints', 'trackings'));
    }
    
    public function addTracking(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        ComplaintTracking::create([
            'complaint_id' => $id,
            'user_id' => auth()->id(),
            'status' => $request->status,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Tracking added');
    }
    public function getTracking($id)
    {
        $trackings = ComplaintTracking::where('complaint_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($trackings);
    }

    public function storeTrackingComment(Request $request)
    {
        $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'comment' => 'required|string|max:1000',
        ]);

        $tracking = new ComplaintTracking();
        $tracking->complaint_id = $request->complaint_id;
        $tracking->action_type = 'comment';
        $tracking->comment = $request->comment;
        $tracking->performed_by = Auth::user()->name;
        $tracking->save();

        return response()->json([
            'success' => true,
            'by' => $tracking->performed_by,
            'comment' => $tracking->comment,
            'time' => $tracking->created_at->format('d-M h:i A'),
        ]);
    }
}
