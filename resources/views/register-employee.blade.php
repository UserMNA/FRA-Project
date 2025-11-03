<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    @extends('app')

    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Registrasikan Pekerja Baru</div>
                    <div class="card-body">
                        <form action="/register-employee" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Pilih Foto (Format: nama_id.jpg/png)</label>
                                <input type="file" class="form-control" id="image" name="image" required accept="image/jpeg,image/jpg,image/png">
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Pekerja</label>
                                <input type="text" class="form-control" id="name" name="name" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">ID Pekerja</label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Posisi</label>
                                <select class="form-select" id="title" name="title" required>
                                    <option value="" selected disabled>Pilih Posisi</option>
                                    <option value="Pemimpin">Pemimpin</option>
                                    <option value="Wakil">Wakil</option>
                                    <option value="Sekuriti">Sekuriti</option>
                                    <option value="Atmin">Atmin</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Pekerja">Pekerja</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Register Pekerja</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('image').addEventListener('change', function(event) {
                const fileInput = event.target;
                if (!fileInput.files || fileInput.files.length === 0) {
                    return;
                }
                
                const fileName = fileInput.files[0].name;
                const label = fileName.substring(0, fileName.lastIndexOf('.')) || fileName; 
                const parts = label.split('_');

                const nameInput = document.getElementById('name');
                const idInput = document.getElementById('employee_id');

                if (parts.length === 2 && parts[0] && parts[1]) {
                    nameInput.value = parts[0];
                    idInput.value = parts[1];
                    nameInput.disabled = false;
                    idInput.disabled = false;
                } else {
                    alert('Invalid file name format. Please use "name_id.jpg/png".');
                    nameInput.value = '';
                    idInput.value = '';
                    nameInput.disabled = true;
                    idInput.disabled = true;
                    fileInput.value = ''; 
                }
            });
        </script>
    @endsection
</body>
</html>