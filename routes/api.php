<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::post('/attendance', [AttendanceController::class, 'store']);
Route::get('/attendance', [AttendanceController::class, 'index']);
Route::get('/attendance/today', [AttendanceController::class, 'today']);

// Route::get('/test', function () {
//     return response()->json(['message' => 'API is working!']);
// });