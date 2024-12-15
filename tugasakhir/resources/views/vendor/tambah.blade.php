<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Percetakan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('../assets/vendor/libs/jquery/jquery.js')}}"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card mx-auto shadow-sm" style="max-width: 500px;">
            <div class="card-body">
                <h3 class="text-center mb-4">Tambah Vendor</h3>
                <form method="POST" action="{{route('tambahvendor')}}" enctype="multipart/form-data">
                    @csrf
                    <!-- Nama Percetakan -->
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Percetakan</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Percetakan" required>
                    </div>
                    
                    <!-- Foto Percetakan -->
                    <div class="mb-3">
                        <label for="fotopercetakan" class="form-label">Foto Percetakan</label>
                        <input type="file" class="form-control" id="fotopercetakan" name="fotopercetakan" accept="image/*" required>
                    </div>

                    <!-- Lokasi Percetakan -->
                    <div class="mb-3">
                        <label for="latitude" class="form-label">Lintang</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" required>
                    </div>
                    <div class="mb-3">
                        <label for="longitude" class="form-label">Bujur</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" required>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mb-3" onclick="getCurrentLocation()">Gunakan Lokasi Anda</button>

                    <!-- Submit -->
                    <input type="submit" class="btn btn-success w-100" value="Tambah Vendor">
                </form>
                @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script>
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $('#latitude').val(position.coords.latitude);
                    $('#longitude').val(position.coords.longitude);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
    </script>
</body>
</html>
