<?php

use Illuminate\Support\Facades\Route;
use App\Models\Attendance;

Route::get('/face', function () {
    return view('face');
});

Route::get('/attendance', function () {
    $attendances = Attendance::latest()->get(); // or paginate()
    return view('attendance', compact('attendances'));
});

// Route::get('/', function () {
//     return view('welcome');
// });
