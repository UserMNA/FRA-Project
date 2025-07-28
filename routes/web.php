<?php

use Illuminate\Support\Facades\Route;

Route::get('/face', function () {
    return view('face');
});

Route::get('/attendance', function () {
    return view('attendance');
});


// Route::get('/', function () {
//     return view('welcome');
// });
