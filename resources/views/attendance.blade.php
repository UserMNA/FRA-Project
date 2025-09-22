<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Absen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h1 class="mb-2">Catatan Absen</h1>

    <form class="mb-4" id="filter-form">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Search by name" id="filter-name">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Search by ID" id="filter-id">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="filter-date">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    <a href="{{ route('attendance.download') }}" class="btn btn-success mb-2">📥 Download Excel</a>
    <a href="{{ route('attendance.pdf') }}" class="btn btn-danger mb-2">🖨️ Download PDF</a>
    <form action="{{ route('attendance.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all attendance records? This action cannot be undone.');" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-warning mb-2">🔄 Clear All Records</button>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>ID</th>
                <th>File</th>
                <th>Posisi</th>
                <th>Di Scan saat</th>
            </tr>
        </thead>
        <tbody id="attendance-body"></tbody>
    </table>

    <script>
        document.getElementById('filter-form').addEventListener('submit', function(event) {
            event.preventDefault();
            loadAttendance();
        });
        
        async function loadAttendance() {
            const name = document.getElementById('filter-name').value;
            const id = document.getElementById('filter-id').value;
            const date = document.getElementById('filter-date').value;

            // Build the query string
            const params = new URLSearchParams();
            if (name) params.append('name', name);
            if (id) params.append('employee_id', id);
            if (date) params.append('date', date);

            const queryString = params.toString();
            const url = `http://127.0.0.1:8000/api/attendance?${queryString}`;

            const res = await fetch(url);
            if (!res.ok) {
                const text = await res.text();
                console.error('Fetch failed:', res.status, text);
                return;
            }
            const json = await res.json();
            const attendanceList = json.data;
            const tbody = document.getElementById('attendance-body');
            tbody.innerHTML = '';

            let lastDate = null;

            attendanceList.forEach(item => {
                const scannedDate = new Date(item.scanned_at);
                const currentDateString = scannedDate.toLocaleDateString();

                if (currentDateString !== lastDate) {
                    tbody.innerHTML += `
                        <tr>
                            <td colspan="5" class="bg-primary text-white text-center fw-bold">
                                ${currentDateString}
                            </td>
                        </tr>
                    `;
                    lastDate = currentDateString;
                }

                tbody.innerHTML += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.employee_id}</td>
                        <td>${item.label}.jpg</td>
                        <td>${item.title ?? 'Employee'}</td>
                        <td>${scannedDate.toLocaleTimeString()}</td>
                    </tr>`;
            });
        }
        loadAttendance();
        setInterval(loadAttendance, 5000);
    </script>
</body>
</html>
