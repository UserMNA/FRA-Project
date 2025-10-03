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
    public function getAttendanceApi(Request $request)
    {
        // Start a query on the Attendance model and eager load the 'employee' relationship
        $query = Attendance::with('employee');

        // Filter logic remains the same
        if ($request->filled('name')) {
            // Filter by name using the relationship
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

        // Order the results and get the data
        $attendance = $query->orderBy('scanned_at')->get();

        // Now, transform the data to include Master Data attributes
        $formattedAttendance = $attendance->map(function ($record) {
            return [
                'name' => $record->employee->name,         // Fetched from Master Data
                'employee_id' => $record->employee_id,     // Stored in Transactional Data
                'title' => $record->employee->title,       // Fetched from Master Data
                'scanned_at' => $record->scanned_at,
                // The label/file name logic will need adjustment depending on your exact API needs
                'label' => $record->employee->name . '_' . $record->employee_id, 
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
    
    public function clearAttendance() {
        Attendance::truncate();
        return redirect()->back()->with('success', 'All attendance records have been cleared.');
    }

    public function clearAttendanceByDate(string $date)
    {
        try {
            // Parse the date and ensure we delete everything for that specific day
            $targetDate = Carbon::parse($date)->toDateString();
            
            Attendance::whereDate('scanned_at', $targetDate)->delete();

            return response()->json(['message' => "Attendance for $targetDate cleared successfully."], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to clear attendance records.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download Excel report for a specific date.
     * (Requires you to adjust your existing download logic to use the $date parameter)
     */
    public function downloadExcel(string $date)
    {
        // Example logic:
        $targetDate = Carbon::parse($date)->toDateString();
        // Adjust your Excel export class to accept the date and filter the data
        // return Excel::download(new AttendanceExport($targetDate), "Attendance_{$targetDate}.xlsx");
        
        return redirect()->back()->with('success', "Download Excel for {$targetDate} initiated.");
    }

    /**
     * Download PDF report for a specific date.
     */
    public function downloadPDF(string $date)
    {
        // Example logic:
        $targetDate = Carbon::parse($date)->toDateString();
        // Adjust your PDF generation logic to accept the date and filter the data
        
        return redirect()->back()->with('success', "Download PDF for {$targetDate} initiated.");
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
