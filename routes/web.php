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
 


