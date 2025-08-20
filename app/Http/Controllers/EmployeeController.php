<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register-employee');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|string|unique:employees|max:50',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048', // 2MB max
        ]);

        $label = "{$request->name}_{$request->employee_id}";
        $filename = "{$label}.jpg";
        $path = $request->file('image')->storeAs('public/labels', $filename);

        // Employee::create([
        //     'name' => $request->name,
        //     'employee_id' => $request->employee_id,
        //     'image_path' => $filename,
        // ]);

        return redirect()->back()->with('success', 'Employee registered successfully!');
    }
}
