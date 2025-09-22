<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pekerja Tercatat</title>
</head>
<body>
    @extends('app')

    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4">Pekerja Tercatat</h2>

                <form class="mb-4" method="GET" action="{{ route('employees.list') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="name" placeholder="Search by name" value="{{ request('name') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="employee_id" placeholder="Search by ID" value="{{ request('employee_id') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>ID Pekerja</th>
                                <th>Posisi</th>
                                <th>File Foto</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->employee_id }}</td>
                                    <td>{{ $employee->title }}</td>
                                    <td>
                                        <img src="{{ asset('storage/labels/' . $employee->image_path) }}" alt="{{ $employee->name }}" style="width: 100px; height: auto;">
                                    </td>
                                    <td>
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
</body>
</html>