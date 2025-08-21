<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Employee;

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

        // if ($request->hasFile('image')) {
        //     dd('File is present in the request!'); 
        // } else {
        //     dd('File is NOT present in the request!');
        // }

        $label = "{$request->name}_{$request->employee_id}";
        $filename = "{$label}.JPG";
        $path = $request->file('image')->storeAs('public/labels', $filename);

        Employee::create([
            'name' => $request->name,
            'employee_id' => $request->employee_id,
            'image_path' => 'labels/' . $filename,
        ]);

        return redirect()->back()->with('success', 'Employee registered successfully!');
    }

    public function showEmployeeList()
    {
        $employees = Employee::all();
        
        return view('employee-list', compact('employees'));
    }

    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id); 
        
        Storage::disk('public')->delete('labels/' . $employee->image_path);

        $employee->delete(); 

        return redirect()->back()->with('success', 'Employee deleted successfully!');
    }
}
