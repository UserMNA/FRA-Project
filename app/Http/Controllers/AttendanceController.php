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
    public function getAttendanceApi(Request $request) {
        $query = Attendance::with('employee');

        $query->whereHas('employee'); 

        if ($request->filled('name')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('name') . '%');
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('date')) {
            $date = Carbon::parse($request->input('date'))->toDateString();
            $query->whereDate('scanned_at', $date);
        }

        $attendance = $query->orderBy('scanned_at')->get();

        $formattedAttendance = $attendance->map(function ($record) {
            // $record->employee is guaranteed to be NOT null now
            return [
                'name' => $record->employee->name,
                'employee_id' => $record->employee_id,
                'title' => $record->employee->title,
                'scanned_at' => $record->scanned_at,
            ];
        });

        return response()->json([
            'data' => $formattedAttendance,
        ]);
    }

    public function showView() {
        $attendances = \App\Models\Attendance::latest()->get();
        return view('attendance', compact('attendances'));
    }
    
    // public function clearAttendance() {
    //     Attendance::truncate();
    //     return redirect()->back()->with('success', 'All attendance records have been cleared.');
    // }    

    // public function downloadExcel(string $date)
    // {
    //     $targetDate = Carbon::parse($date)->toDateString();

    //     return redirect()->back()->with('success', "Download Excel for {$targetDate} initiated.");
    // }

    // public function downloadPDF(string $date)
    // {
    //     $targetDate = Carbon::parse($date)->toDateString();
        
    //     return redirect()->back()->with('success', "Download PDF for {$targetDate} initiated.");
    // }

    public function downloadExcel(string $date)
    {
        $targetDate = Carbon::parse($date)->toDateString();
        $filename = "attendance_{$targetDate}.xlsx";

        // 💡 FIX: Use Maatwebsite\Excel to serve the file
        // Assuming you have an AttendanceExport class that filters by date
        return Excel::download(new AttendanceExport($targetDate), $filename); 
    }

    public function downloadPDF(string $date)
    {
        $targetDate = Carbon::parse($date)->toDateString();
        
        // 💡 FIX: Use DomPDF to generate and serve the PDF
        // You'll need to fetch the data and pass it to a view for rendering
        $attendanceData = Attendance::whereDate('scanned_at', $targetDate)->with('employee')->get();
        
        $pdf = PDF::loadView('pdf.attendance-report', compact('attendanceData', 'targetDate'));
        
        return $pdf->download("attendance_{$targetDate}.pdf");
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'name' => 'required|string',
            'label' => 'required|string',
            'title' => 'nullable|string',
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
    
    public function index() {
        $attendances = Attendance::latest()->get();

        return response()->json([
            'message' => 'Attendance list retrieved successfully.',
            'data' => $attendances
        ], 200);
    }
}
