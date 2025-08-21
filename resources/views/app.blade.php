<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand text-primary fw-bold" href="/">Attendance App</a>
            <div>
                <a class="btn btn-primary me-2" href="/face">Scan Face</a>
                <a class="btn btn-outline-primary me-2" href="/register-employee">Register Employee</a>
                <a class="btn btn-success me-2" href="/attendance-view">Attendance</a>
                <a class="btn btn-outline-success" href="/employees">Employees List</a>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>