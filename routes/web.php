<?php

use Illuminate\Support\Facades\Route;
use App\Models\Attendance;
use App\Http\Controllers\AttendanceController;

Route::get('/attendance-view', [AttendanceController::class, 'showView']);

Route::get('/face', function () {
    return view('face');
});
