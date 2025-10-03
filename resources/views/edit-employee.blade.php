<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
</head>
<body>
    @extends('app')

    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Edit Pekerja</div>
                    <div class="card-body">
                        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Pekerja</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">ID Pekerja</label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Position</label>
                                <select class="form-select" id="title" name="title" required>
                                    <option value="Leader" {{ old('title', $employee->title) == 'Leader' ? 'selected' : '' }}>Pemimpin</option>
                                    <option value="Vice-Leader" {{ old('title', $employee->title) == 'Vice-Leader' ? 'selected' : '' }}>Wakil</option>
                                    <option value="Security" {{ old('title', $employee->title) == 'Security' ? 'selected' : '' }}>Sekuriti</option>
                                    <option value="Admin" {{ old('title', $employee->title) == 'Admin' ? 'selected' : '' }}>Atmin</option>
                                    <option value="Manager" {{ old('title', $employee->title) == 'Secretary' ? 'selected' : '' }}>Sekretaris</option>
                                    <option value="Employee" {{ old('title', $employee->title) == 'Employee' ? 'selected' : '' }}>Pekerja</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Foto Pekerja</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <div class="mt-2">
                                    <img src="{{ asset('storage/labels/' . $employee->image_path) }}" alt="Current Photo" style="width: 150px; height: auto;">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
</html>