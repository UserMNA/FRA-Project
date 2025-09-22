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
            'title' => 'required|string|max:30',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        
        $label = "{$request->name}_{$request->employee_id}";
        $filename = "{$label}.jpg";

        $uploadedFile = $request->file('image');
        
        $fileContent = file_get_contents($uploadedFile->getRealPath());

        Storage::disk('public')->put("labels/{$filename}", $fileContent);

        Employee::create([
            'name' => $request->name,
            'employee_id' => $request->employee_id,
            'title' =>  $request->title,
            'image_path' => $filename,
        ]);

        return redirect()->back()->with('success', 'Employee registered successfully!');
    }

    public function getEmployeesApi()
    {
        $employees = Employee::select('name', 'employee_id', 'title', 'image_path')->get();
        return response()->json($employees);
    }

    public function showEmployeeList(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $employees = $query->get();

        return view('employee-list', compact('employees'));
    }

    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id); 
        
        Storage::disk('public')->delete('labels/' . $employee->image_path);

        $employee->delete(); 

        return redirect()->back()->with('success', 'Employee deleted successfully!');
    }

    public function edit(Employee $employee)
    {
        return view('edit-employee', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50|unique:employees,employee_id,' . $employee->id,
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $employee->name = $request->name;
        $employee->employee_id = $request->employee_id;
        $employee->title = $request->title;

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old file
            Storage::disk('public')->delete('labels/' . $employee->image_path);
            
            // Generate new filename
            $newFilename = "{$request->name}_{$request->employee_id}.jpg";
            $uploadedFile = $request->file('image');
            $fileContent = file_get_contents($uploadedFile->getRealPath());

            // Save new file
            Storage::disk('public')->put("labels/{$newFilename}", $fileContent);
            $employee->image_path = $newFilename;
        } else {
            // Update filename if name or ID changed but no new image was uploaded
            $newFilename = "{$request->name}_{$request->employee_id}.jpg";
            if ($employee->image_path !== $newFilename) {
                Storage::disk('public')->move('labels/' . $employee->image_path, 'labels/' . $newFilename);
                $employee->image_path = $newFilename;
            }
        }
        
        $employee->save();

        return redirect()->route('employees.list')->with('success', 'Employee updated successfully!');
    }
}