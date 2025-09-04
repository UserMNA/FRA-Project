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
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        
        $label = "{$request->name}_{$request->employee_id}";
        $filename = "{$label}.jpg";

        // Get the uploaded file object
        $uploadedFile = $request->file('image');
        
        // Get the content of the uploaded file
        $fileContent = file_get_contents($uploadedFile->getRealPath());

        // Manually save the file content to the public disk
        // This is more reliable for handling permissions on some setups
        Storage::disk('public')->put("labels/{$filename}", $fileContent);

        // Save to the database
        Employee::create([
            'name' => $request->name,
            'employee_id' => $request->employee_id,
            'image_path' => $filename,
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