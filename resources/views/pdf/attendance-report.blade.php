<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Catatan Absen</title>
        <style>
            body { font-family: sans-serif; font-size: 14px; }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
            th {
                background: white;
            }
        </style>
    </head>
    <body>
        <h2>Attendance Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>ID</th>
                    <th>Posisi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $attendance) <tr>
                        <td>{{ $attendance->employee->name ?? 'Karyawan Dihapus' }}</td>
                        
                        <td>{{ $attendance->employee_id }}</td>
                        
                        <td>{{ $attendance->employee->title ?? 'N/A' }}</td>
                        
                        <td>{{ \Carbon\Carbon::parse($attendance->scanned_at)->format('m/d/Y, H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
