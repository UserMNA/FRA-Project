<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function showView() {
        $attendances = \App\Models\Attendance::latest()->get();
        return view('attendance', compact('attendances'));
    }
    
    public function store(Request $request){
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'name' => 'required|string',
            'label' => 'required|string',
            'confidence' => 'nullable|numeric',
            'scanned_at' => 'required|string',
        ]);

        $validated['scanned_at'] = Carbon::parse($validated['scanned_at'])->format('Y-m-d H:i:s');

        $validated['folder_name'] = strtolower($validated['name']) . '_' . $validated['employee_id'];

        $attendance = Attendance::create($validated);

        return response()->json([
            'message' => 'Attendance recorded.',
            'data' => $attendance
        ], 201);
    }
    
    public function index()
    {
        $attendances = Attendance::latest()->get();

        return response()->json([
            'message' => 'Attendance list retrieved successfully.',
            'data' => $attendances
        ], 200);
    }
}
