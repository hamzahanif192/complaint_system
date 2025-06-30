<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplaintModel;
use App\Models\Department;
use App\Models\ComplaintTracking;
use PHPUnit\Framework\MockObject\ReturnValueNotConfiguredException;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;



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

            if (auth()->user()->isAdmin()) {
                if ($req->assigned_department_id && !$req->assigned_employee_id) {
                    $complaint->status = 'Assigned Department';
                } elseif ($req->assigned_department_id && $req->assigned_employee_id) {
                    $complaint->status = 'Assigned';
                }
            } elseif (auth()->user()->isDepartmentHead()) {
                if ($req->assigned_employee_id && $req->assigned_employee_id != $originalEmp) {
                    $complaint->status = 'Assigned';
                }
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

        // Load departments and employees for assignment
        $resolverDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];
        $resolverDeptIds = Department::whereIn('name', $resolverDepartments)->pluck('id');

        if (auth()->user()->isAdmin()) {
            $departments = Department::whereIn('id', $resolverDeptIds)->get();

            $employees = User::where('role', 'employee')
                ->where(function ($q) use ($resolverDeptIds) {
                    $q->whereIn('department_id', $resolverDeptIds)
                        ->orWhere('is_master', true);
                })->get();
        } elseif (auth()->user()->isDepartmentHead()) {
            $departments = Department::where('id', auth()->user()->department_id)->get();
            $employees = User::where('department_id', auth()->user()->department_id)
                ->where('role', 'employee')->get();
        }

        return view('complaints.edit-complaint', compact('complaint', 'departments', 'employees'))
            ->with('complaint_details', $complaint);
    }




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
        $user->is_master = $request->has('is_master');


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

    public function deleteDepartment($id)
    {
        // Find the department by ID
        $department = Department::findOrFail($id);

        // Delete the department
        $department->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Department deleted successfully!');
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

            if (!$complaint) {
                return back()->with('error', 'Complaint not found.');
            }

            if (in_array($complaint->status, ['Resolved', 'On Hold'])) {
                return back()->with('error', 'Status already finalized.');
            }

            $complaint->status = $request->status;
            $complaint->save();

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

        // Resolver department IDs
        $resolverDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];
        $resolverDeptIds = Department::whereIn('name', $resolverDepartments)->pluck('id');

        if (auth()->user()->is_master) {
            // Master resolver sees all resolver complaints
            $complaints = ComplaintModel::whereIn('assigned_department_id', $resolverDeptIds)->get();
        } else {
            // Regular employee sees only their own assigned
            $complaints = ComplaintModel::where('assigned_employee_id', auth()->id())->get();
        }

        $trackings = ComplaintTracking::whereIn('complaint_id', $complaints->pluck('id'))->get();

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

    public function fetchNewComplaints(Request $request)
    {
        $lastId = $request->input('last_id', 0);
        $lastChecked = $request->input('last_checked', now()->subMinutes(5)); // Fallback time

        // Role wise filter       

        if (auth()->user()->isAdmin()) {
            $complaints = ComplaintModel::with(['assignedDepartment', 'assignedEmployee'])
                ->where('updated_at', '>', $lastChecked)
                ->orderBy('updated_at', 'asc')
                ->get();
        } elseif (auth()->user()->isDepartmentHead()) {
            $complaints = ComplaintModel::with(['assignedDepartment', 'assignedEmployee'])
                ->whereHas('assignedDepartment', function ($q) {
                    $q->where('id', auth()->user()->department_id);
                })
                ->where('updated_at', '>', $lastChecked)
                ->orderBy('updated_at', 'asc')
                ->get();
        } elseif (auth()->user()->isEmployee()) {
            $complaints = ComplaintModel::with(['assignedDepartment', 'assignedEmployee'])
                ->where('assigned_employee_id', auth()->id())
                ->where('updated_at', '>', $lastChecked)
                ->orderBy('updated_at', 'asc')
                ->get();
        } else {
            $complaints = collect(); // Koi nahi milega
        }

        $trackings = ComplaintTracking::all();

        $html = '';

        foreach ($complaints as $single) {
            $html .= view('partials.complaint_row', [
                'single' => $single,
                'trackings' => $trackings,
                'newlyAdded' => true
            ])->render();
        }

        return response()->json([
            'html' => $html,
            'last_id' => $complaints->max('id') ?? $lastId,
            'last_checked' => now()->toDateTimeString()
        ]);
    }


    // complaint Reporting
    public function complaintReport(Request $request)
    {
        $resolverDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];

        // Load all resolver departments
        $departments = Department::whereIn('name', $resolverDepartments)->get();

        // Summary counts
        $totalComplaints = ComplaintModel::count();
        $resolvedComplaints = ComplaintModel::where('status', 'Resolved')->count();

        // Accurate average resolution time from complaint_trackings
        $avgTime = DB::table('complaint_trackings')
            ->join('complaints', 'complaint_trackings.complaint_id', '=', 'complaints.id')
            ->where('complaint_trackings.action_type', 'status_update')
            ->where('complaint_trackings.comment', 'like', '%Resolved%')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, complaints.created_at, complaint_trackings.created_at)) as avg_seconds')
            ->value('avg_seconds');

        $averageResolutionTime = $avgTime
            ? gmdate("H:i:s", $avgTime)
            : "N/A";

        // Department-wise report
        $departmentsForReport = Department::whereIn('name', $resolverDepartments)
            ->with('complaints')->get();

        $departmentReport = $departmentsForReport->map(function ($dept) {
            $total = $dept->complaints->count();
            $resolved = $dept->complaints->where('status', 'Resolved')->count();
            $queue = $dept->complaints->whereIn('status', ['In Progress', 'On Hold', 'Pending'])->count();
            $percentResolved = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;

            return [
                'department' => $dept->name,
                'department_id' => $dept->id,
                'queue' => $queue,
                'resolved' => $resolved,
                'total' => $total,
                'percent' => $percentResolved
            ];
        });

        return view('reports.complaint_report', compact(
            'departmentReport',
            'departments',
            'totalComplaints',
            'resolvedComplaints',
            'averageResolutionTime'
        ));
    }

    public function departmentComplaints($departmentId, Request $request)
    {
        $department = Department::findOrFail($departmentId);

        $query = ComplaintModel::with(['assignedDepartment', 'assignedEmployee', 'user'])
            ->where('assigned_department_id', $departmentId);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $complaints = $query->paginate(10);

        return view('reports.report_department', compact('complaints', 'departmentId', 'department'));
    }

    public function generatePdf($departmentId, Request $request)
    {
        $department = Department::findOrFail($departmentId);

        // Filtered complaints
        $query = ComplaintModel::with('assignedDepartment')
            ->where('assigned_department_id', $departmentId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $complaints = $query->get();

        // Load tracking once (for resolution date per complaint)
        $trackings = ComplaintTracking::whereIn('complaint_id', $complaints->pluck('id'))->get();

        // Summary values
        $total = $complaints->count();
        $resolved = $complaints->where('status', 'Resolved')->count();
        $queue = $complaints->whereIn('status', ['In Progress', 'On Hold', 'Pending'])->count();

        // Avg resolution time from trackings
        $avgTime = DB::table('complaint_trackings')
            ->join('complaints', 'complaint_trackings.complaint_id', '=', 'complaints.id')
            ->whereIn('complaints.id', $complaints->pluck('id'))
            ->where('complaint_trackings.action_type', 'status_update')
            ->where('complaint_trackings.comment', 'like', '%Resolved%')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, complaints.created_at, complaint_trackings.created_at)) as avg_seconds')
            ->value('avg_seconds');

        $averageResolutionTime = $avgTime
            ? gmdate("H:i:s", $avgTime)
            : "N/A";

        $pdf = Pdf::loadView('pdf.department_pdf', compact(
            'complaints',
            'trackings',
            'total',
            'resolved',
            'queue',
            'averageResolutionTime'
        ));

        return $pdf->download('complaint-report.pdf');
    }
    public function userComplaintReport(Request $request)
    {
        $resolverDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];
        $resolverDeptIds = Department::whereIn('name', $resolverDepartments)->pluck('id');

        // Get resolver employees only
        $employees = User::whereIn('department_id', $resolverDeptIds)
            ->where('role', 'employee')->get();

        $query = ComplaintModel::query()->with(['assignedEmployee', 'assignedDepartment']);

        // Only resolver complaints
        $query->whereIn('assigned_department_id', $resolverDeptIds);

        if ($request->filled('user_id')) {
            $query->where('assigned_employee_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get();

        // Summary counts
        $resolvedCount = $complaints->where('status', 'Resolved')->count();
        $pendingCount = $complaints->whereIn('status', ['Pending', 'On Hold', 'In Progress'])->count();
        $totalCount = $complaints->count();

        // Calculate total hours from assign to resolved
        $totalSeconds = 0;
        foreach ($complaints as $c) {
            $assigned = ComplaintTracking::where('complaint_id', $c->id)
                ->where('action_type', 'assign_employee')->first();
            $resolved = ComplaintTracking::where('complaint_id', $c->id)
                ->where('action_type', 'status_update')
                ->where('comment', 'like', '%Resolved%')->first();

            if ($assigned && $resolved) {
                $totalSeconds += $resolved->created_at->diffInSeconds($assigned->created_at);
            }
        }

        $totalWorkingHours = gmdate("H:i:s", $totalSeconds);

        return view('reports.report_user', compact(
            'employees',
            'complaints',
            'resolvedCount',
            'pendingCount',
            'totalCount',
            'totalWorkingHours'
        ));
    }
    public function exportUserComplaintPDF(Request $request)
    {
        $resolverDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];
        $resolverDeptIds = Department::whereIn('name', $resolverDepartments)->pluck('id');

        $query = ComplaintModel::query()->with(['assignedEmployee', 'assignedDepartment']);

        $query->whereIn('assigned_department_id', $resolverDeptIds);

        if ($request->filled('user_id')) {
            $query->where('assigned_employee_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get();

        $resolvedCount = $complaints->where('status', 'Resolved')->count();
        $pendingCount = $complaints->whereIn('status', ['Pending', 'In Progress', 'On Hold'])->count();
        $totalCount = $complaints->count();

        $totalSeconds = 0;
        foreach ($complaints as $c) {
            $assigned = ComplaintTracking::where('complaint_id', $c->id)
                ->where('action_type', 'assign_employee')->first();
            $resolved = ComplaintTracking::where('complaint_id', $c->id)
                ->where('action_type', 'status_update')
                ->where('comment', 'like', '%Resolved%')->first();

            if ($assigned && $resolved) {
                $totalSeconds += $resolved->created_at->diffInSeconds($assigned->created_at);
            }
        }

        $totalWorkingHours = gmdate("H:i:s", $totalSeconds);

        $pdf = Pdf::loadView('reports.report_user_pdf', compact(
            'complaints',
            'resolvedCount',
            'pendingCount',
            'totalCount',
            'totalWorkingHours'
        ));

        return $pdf->download('user_report.pdf');
    }
    public function startJob($id)
    {
        $complaint = ComplaintModel::findOrFail($id);

        if ($complaint->assigned_employee_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $complaint->status = 'In Progress';
        $complaint->save();

        ComplaintTracking::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->id(),
            'action_type' => 'status_update',
            'performed_by' => auth()->user()->name,
            'comment' => 'Status changed to In Progress by employee',
        ]);

        return response()->json(['success' => true]);
    }
    public function updateStatus(Request $request, $id)
    {
        $complaint = ComplaintModel::findOrFail($id);
        if ($complaint->assigned_employee_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $status = $request->status;
        if (!in_array($status, ['On Hold', 'Resolved'])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }
        $complaint->status = $status;
        $complaint->save();
        ComplaintTracking::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->id(),
            'action_type' => 'status_update',
            'performed_by' => auth()->user()->name,
            'comment' => "Status changed to $status by employee"
        ]);

        return response()->json(['success' => true]);
    }
}
