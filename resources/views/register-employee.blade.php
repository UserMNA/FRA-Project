<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @extends('app')

    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Register New Employee</div>
                    <div class="card-body">
                        <form action="/register-employee" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Employee Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Employee Photo</label>
                                <input type="file" class="form-control" id="image" name="image" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Register Employee</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
</html>