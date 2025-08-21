<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/face', function () {
    return view('face');
});
Route::get('/attendance-download', [AttendanceController::class, 'downloadExcel'])->name('attendance.download');
Route::get('/attendance-pdf', [AttendanceController::class, 'downloadPDF'])->name('attendance.pdf');
Route::get('/attendance-view', [AttendanceController::class, 'showView']);
Route::get('/register-employee', [EmployeeController::class, 'showRegistrationForm']);
Route::post('/register-employee', [EmployeeController::class, 'register']);
Route::get('/employees', [EmployeeController::class, 'showEmployeeList'])->name('employees.list');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');