<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/attendance-download', [AttendanceController::class, 'downloadExcel'])->name('attendance.download');
Route::get('/attendance-pdf', [AttendanceController::class, 'downloadPDF'])->name('attendance.pdf');
Route::get('/attendance-view', [AttendanceController::class, 'showView']);
Route::get('/face', function () {
    return view('face');
});