<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function showView() {
        $attendances = \App\Models\Attendance::latest()->get();
        return view('attendance', compact('attendances'));
    }

    public function downloadExcel()
    {
        return Excel::download(new AttendanceExport, 'attendance.xlsx');
    }

    public function downloadPDF()
    {
        $attendances = Attendance::all();
        $pdf = Pdf::loadView('attendance-pdf', compact('attendances'));
        return $pdf->download('attendance_report.pdf');
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

        $validated['label'] = strtolower($validated['name']) . '_' . $validated['employee_id'];

        $today = Carbon::now()->toDateString();

        $existing = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('scanned_at', $today)
            ->first();

        if ($existing) {
            $existing->update($validated);
            $attendance = $existing;
            $message = 'Attendance updated.';
            $status = 200;
        } else {
            $attendance = Attendance::create($validated);
            $message = 'Attendance recorded.';
            $status = 201;
        }

        return response()->json([
            'message' => $message,
            'data' => $attendance
        ], $status);
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
