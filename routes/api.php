<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;

Route::post('/attendance', [AttendanceController::class, 'store']);
Route::get('/attendance', [AttendanceController::class, 'index']);
Route::get('/attendance/today', [AttendanceController::class, 'today']);
Route::get('/employees/labels', [EmployeeController::class, 'getLabels']);