<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Attendance Report</title>
        <style>
            body { font-family: sans-serif; font-size: 14px; }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #333;
                padding: 8px;
                text-align: left;
            }
            th {
                background: #eee;
            }
        </style>
    </head>
    <body>
        <h2>Attendance Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Scanned At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->name }}</td>
                        <td>{{ $attendance->employee_id }}</td>
                        <td>{{ $attendance->label }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->scanned_at)->format('m/d/Y, h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
