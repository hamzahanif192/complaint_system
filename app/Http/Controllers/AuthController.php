<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
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
    
        Auth::login($user);
    
        return redirect('/dashboard')->with('success', 'Signup successful!');
    }
    
}
