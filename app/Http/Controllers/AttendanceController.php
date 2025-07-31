<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return Attendance::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = 
        $request->validate([
            'employee_id' => 'required|string',
            'name' => 'required|string',
            'label' => 'required|string',
            'confidence' => 'nullable|numeric',
            'scanned_at' => 'required|date',
        ]);

        return Attendance::create($validated);

        $scannedAt = Carbon::parse($request->input('scanned_at'))->toDateTimeString();

        Attendance::create([
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'label' => $request->input('label'),
            'confidence' => $request->input('confidence'),
            'scanned_at' => $scannedAt,
        ]);

        return response()->json(['message' => 'Attendance recorded.']);

        // return response()->json(['message' => 'Attendance recorded'], 201);
    }
}