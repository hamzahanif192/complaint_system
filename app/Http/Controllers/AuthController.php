<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\ComplaintTracking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show the signup form
    public function showSignup()
    {
        $departments = Department::all();
        return view('signup', compact('departments'));
    }

    // Handle user signup
    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'department_id' => 'required|exists:departments,id',
        ]);
        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'employee',
                'department_id' => $request->department_id,
            ]);

            // Auth::login($user);

            // return redirect()->back()->with('success', 'User has been successfully added!');
            return redirect()->back()
            ->with('success', 'User created successfully!')
        ->with('user_created', $request->email);

        
    }
    // public function manageUsers()
    // {
    //     $users_general = User::all(); // ya tumhari filtered query
    //     $users_special = collect();   // agar chaho to use empty for now
    //     $departments = Department::all();
    
    //     return view('manage-users', [
    //         'users_general' => $users_general,
    //         'users_special' => $users_special,
    //         'departments' => $departments
    //     ]);
    // }

//     public function manageUsers()
// {
//     return "Reached manageUsers method!";
// }
public function manageUsers()
{
    $departments = Department::all();
    $users = User::with('department')->get();

    return view('manage-users', compact('users', 'departments'));
}


    // condition  for dashboard for user complaint and consignee user
    public function employeeDashboard()
    {
        $user = Auth::user();

        $fixedDepartments = ['Electrical', 'Mechanical', 'Plumbing', 'IT', 'Maintenance Dept', 'Construction Work'];

        if (in_array(optional($user->department)->name, $fixedDepartments)) {
            return view('complaint_consignee.consignee_dashboard');
        } else {
            return view('user_complaint.home');
        }
    }
    public function addTracking(Request $request, $complaintId)
    {
        $request->validate([
            'status' => 'required|string|max:255',
            'comment' => 'nullable|string',
        ]);

        ComplaintTracking::create([
            'complaint_id' => $complaintId,
            'user_id' => auth()->id(),
            'status' => $request->status,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Tracking information added successfully.');
    }

    public function getTracking($id)
    {
        $trackings = ComplaintTracking::where('complaint_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($trackings);
    }

}
