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
                                    <option value="Pemimpin" {{ old('title', $employee->title) == 'Pemimpin' ? 'selected' : '' }}>Pemimpin</option>
                                    <option value="Wakil" {{ old('title', $employee->title) == 'Wakil' ? 'selected' : '' }}>Wakil</option>
                                    <option value="Sekuriti" {{ old('title', $employee->title) == 'Sekuriti' ? 'selected' : '' }}>Sekuriti</option>
                                    <option value="Atmin" {{ old('title', $employee->title) == 'Atmin' ? 'selected' : '' }}>Atmin</option>
                                    <option value="Sekretaris" {{ old('title', $employee->title) == 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                                    <option value="Pekerja" {{ old('title', $employee->title) == 'Pekerja' ? 'selected' : '' }}>Pekerja</option>
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