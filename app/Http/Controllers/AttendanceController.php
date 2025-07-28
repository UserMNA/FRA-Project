<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        // Fetch today's attendance
        $today = Attendance::whereDate('scanned_at', now())->get();
        return response()->json($today);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|string',
            'name' => 'required|string',
            'label' => 'required|string',
            'confidence' => 'nullable|numeric',
            'scanned_at' => 'required|date',
        ]);

        $attendance = Attendance::create($data);

        return response()->json($attendance, 201);
    }
}