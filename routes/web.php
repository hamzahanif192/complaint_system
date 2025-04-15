<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;



Route::get('/login', [AdminController::class, 'showLogin'])->name('login'); 

Route::post('/login', [AdminController::class, 'handleLogin']); 

Route::group(['middleware' => 'auth'], function (){
    Route::get('/dashboard', [AdminController::class, 'dashboard']); 
    Route::get('/logout', [AdminController::class, 'logout']); 


    // Route::group(['prefix' => 'complaints'],function (){
    // Route::get('/add-complaint', [AdminController::class, 'add_complaint']); 
    // });
    Route::group(['prefix' => 'complaints'], function () {
        Route::get('/add-complaint', [AdminController::class, 'add_complaint']);
        Route::post('/add-complaint', [AdminController::class, 'add_complaint_req']);
        Route::get('/view-complaint', [AdminController::class, 'view_complaint']);
        Route::get('/delete-complaint/{id}', [AdminController::class, 'delete_complaint']);
        Route::get('/edit-complaint/{id}', [AdminController::class, 'edit_complaint']);
        Route::post('/edit-complaint/{id}', [AdminController::class, 'edit_complaint']);
    });

        // Signup routes
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'registerUser']);

    Route::group(['middleware' => 'auth', ], function () {
        Route::get('/users', [AdminController::class, 'manageUsers']);
        Route::post('/users/{id}/update', [AdminController::class, 'updateUserRole']);
    });
    Route::get('/add_department', [AdminController::class, 'addDepartmentForm']);
    Route::post('/add_department', [AdminController::class, 'storeDepartment']);

});
 

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']); // Admin & Head

    Route::get('/dashboard/employee', function () {
        return view('user_complaint.home');
    });

    Route::get('/dashboard/employee/resolver', function () {
        return view('complaint_consignee.consignee_dashboard');
    });
});
// Route::post('/complaints/submit', [ComplaintController::class, 'store'])->name('complaints.submit');

Route::middleware(['auth'])->group(function () {
    Route::post('/employee/complaint-submit', [AdminController::class, 'employeeSubmitComplaint'])->name('employee.complaint.submit');
    
});
Route::get('/dashboard/employee', [AdminController::class, 'employeeDashboard'])->name('employee.dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::get('/my-complaints', [AdminController::class, 'userComplaints'])->name('user.complaints');
//     // Yahan aur bhi authenticated routes add kar sakte hain
// });
Route::get('/dashboard/employee', [AdminController::class, 'employeeDashboardview'])->middleware('auth');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/employee/resolver', [AdminController::class, 'employeeResolverView'])->name('employee.resolver.dashboard');
});
// Route::post('/complaints/{id}/tracking', [AdminController::class, 'addTracking'])->name('add.tracking');
Route::post('/complaints/track-comment', [AdminController::class, 'storeTrackingComment'])->name('store.tracking.comment');
Route::get('/complaints/tracking/{id}', [AdminController::class, 'getTracking'])->name('get.tracking');



