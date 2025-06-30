<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;



Route::get('/login', [AdminController::class, 'showLogin'])->name('login');

Route::post('/login', [AdminController::class, 'handleLogin']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/logout', [AdminController::class, 'logout']);

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

    Route::group(['middleware' => 'auth',], function () {
        Route::get('/users', [AdminController::class, 'manageUsers']);
        Route::post('/users/{id}/update', [AdminController::class, 'updateUserRole']);
    });
    Route::get('/add_department', [AdminController::class, 'addDepartmentForm']);
    Route::post('/add_department', [AdminController::class, 'storeDepartment']);
    Route::delete('/delete-department/{id}', [AdminController::class, 'deleteDepartment'])->name('delete.department');

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

Route::middleware(['auth'])->group(function () {
    Route::post('/employee/complaint-submit', [AdminController::class, 'employeeSubmitComplaint'])->name('employee.complaint.submit');
});
Route::get('/dashboard/employee', [AdminController::class, 'employeeDashboard'])->name('employee.dashboard');

// });
Route::get('/dashboard/employee', [AdminController::class, 'employeeDashboardview'])->middleware('auth');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/employee/resolver', [AdminController::class, 'employeeResolverView'])->name('employee.resolver.dashboard');
    Route::post('/dashboard/employee/resolver', [AdminController::class, 'employeeResolverView'])->name('employee.resolver.dashboard');
});
// Tracking
Route::post('/complaints/track-comment', [AdminController::class, 'storeTrackingComment'])->name('store.tracking.comment');
Route::get('/complaints/tracking/{id}', [AdminController::class, 'getTracking'])->name('get.tracking');

// fetch 
Route::get('/admin/fetch-new-complaints', [AdminController::class, 'fetchNewComplaints'])->name('admin.fetch.new.complaints');
Route::get('/admin/get-new-complaints', [AdminController::class, 'fetchNewComplaints'])->name('admin.get_new_complaints');
Route::get('/fetch-new-complaints', [AdminController::class, 'fetchNewComplaints'])->name('fetch.new.complaints');

// Complaint Report
Route::get('/complaint-report', [AdminController::class, 'complaintReport'])->name('complaint.report');
Route::get('/complaint-report/{id}/department', [AdminController::class, 'departmentComplaints'])->name('complaint.department');
Route::get('/complaint-report/{id}/pdf', [AdminController::class, 'generatePdf'])->name('complaint.pdf');


Route::get('/user-report', [AdminController::class, 'userComplaintReport'])->name('report.user');
Route::get('/user-report/pdf', [AdminController::class, 'exportUserComplaintPDF'])->name('report.user.pdf');

Route::post('/employee/start-job/{id}', [AdminController::class, 'startJob'])->name('employee.start.job');
Route::post('/employee/status-update/{id}', [AdminController::class, 'updateStatus'])->name('employee.status.update');

