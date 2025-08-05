<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h1 class="mb-2">Attendance Records</h1>
    <a href="{{ route('attendance.download') }}" class="btn btn-success mb-2">📥 Download Excel</a>
    <a href="{{ route('attendance.pdf') }}" class="btn btn-danger mb-2">🖨️ Download PDF</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>ID</th>
                <th>File</th>
                <th>Title</th>
                <th>Scanned At</th>
            </tr>
        </thead>
        <tbody id="attendance-body"></tbody>
    </table>

    <script>
        async function loadAttendance() {   
            const res = await fetch('http://127.0.0.1:8000/api/attendance');
            if (!res.ok) {
                const text = await res.text(); 
                console.error('Fetch failed:', res.status, text);
                return;
            }
            const json = await res.json();
            const attendanceList = json.data;
            const tbody = document.getElementById('attendance-body');
            tbody.innerHTML = '';
            attendanceList.forEach(item => {
                tbody.innerHTML += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.employee_id}</td>
                        <td>${item.label}.jpg</td>
                        <td>${item.title ?? 'Employee' }</td>
                        <td>${new Date(item.scanned_at).toLocaleString()}</td>
                    </tr>`;
            });
        }
        loadAttendance();
        setInterval(loadAttendance, 5000);
    </script>
</body>
</html>
