<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Absen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    
    <!-- <a href="{{ route('attendance.download') }}" class="btn btn-success mb-2">📥 Download Excel</a>
    <a href="{{ route('attendance.pdf') }}" class="btn btn-danger mb-2">🖨️ Download PDF</a>
    <form action="{{ route('attendance.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all attendance records? This action cannot be undone.');" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-warning mb-2">🔄 Clear All Records</button>
    </form> -->
    <div id="attendance-container">
    </div>

    <script>
        let attendanceIntervalId;
        
        document.getElementById('filter-form').addEventListener('submit', function(event) {
            event.preventDefault();
            loadAttendance();
        });

        async function clearRecordsByDate(dateString) {
            const confirmDelete = confirm(`Are you sure you want to clear all records for ${dateString}? This action cannot be undone.`);
            
            if (confirmDelete) {
                const response = await fetch(`/attendance/clear/${dateString}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    alert(`Records for ${dateString} cleared successfully.`);
                    loadAttendance(); 
                } else {
                    alert('Failed to clear records.');
                    const errorText = await response.text();
                    console.error('Clear Records Error:', response.status, errorText);
                }
                attendanceIntervalId = setInterval(loadAttendance, 5000);
            }
        }
        
        async function loadAttendance() {
            const name = document.getElementById('filter-name').value;
            const id = document.getElementById('filter-id').value;
            const date = document.getElementById('filter-date').value;

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
            
            const container = document.getElementById('attendance-container');
            container.innerHTML = ''; 

            let lastDate = null;
            let htmlContent = ''; 

            attendanceList.sort((a, b) => new Date(a.scanned_at) - new Date(b.scanned_at));

            attendanceList.forEach(item => {
                const scannedDate = new Date(item.scanned_at);
                
                const dateFormatOptions = { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'Asia/Makassar' };
                const currentDateString = new Intl.DateTimeFormat('id-ID', dateFormatOptions).format(scannedDate);
                
                const timeFormatOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Makassar' };
                const currentTimeString = new Intl.DateTimeFormat('id-ID', timeFormatOptions).format(scannedDate);

                if (currentDateString !== lastDate) {
                    
                    if (lastDate !== null) {
                        htmlContent += `</tbody></table>`;
                    }

                    const urlDate = scannedDate.toISOString().split('T')[0]; // YYYY-MM-DD
                    
                    htmlContent += `
                        <div class="d-flex justify-content-between align-items-center bg-secondary text-white p-2 mt-4 mb-2 rounded">
                            <h4 class="m-0">📅 Catatan Absen: ${currentDateString}</h4>
                            <div>
                                <a href="/attendance/download/${urlDate}" class="btn btn-success btn-sm me-2">📥 Download Excel</a>
                                <a href="/attendance/pdf/${urlDate}" class="btn btn-danger btn-sm me-2">🖨️ Download PDF</a>
                            </div>
                        </div>
                    `;
                    
                    // 3. Start the new table structure with its header
                    htmlContent += `
                        <table class="table table-bordered mb-5">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>ID</th>
                                    <th>Posisi</th>
                                    <th>Di Scan saat</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    lastDate = currentDateString;
                }

                // 4. Add the row to the current day's table body
                htmlContent += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.employee_id}</td>
                        <td>${item.title ?? 'Employee'}</td>
                        <td>${currentTimeString}</td>
                    </tr>`;
            });

            // 5. After the loop, close the final table if data exists
            if (attendanceList.length > 0) {
                htmlContent += `</tbody></table>`;
            }

            // 6. Inject all generated content into the container once
            container.innerHTML = htmlContent;
        }
        
        document.getElementById('attendance-container').addEventListener('click', function(event) {
            const target = event.target.closest('.clear-day-btn');

            if (target) {
                event.preventDefault();
                const dateString = target.getAttribute('data-date');
                clearRecordsByDate(dateString);
            }
        });

        loadAttendance();
        setInterval(loadAttendance, 5000);
    </script>
</body>
</html>
