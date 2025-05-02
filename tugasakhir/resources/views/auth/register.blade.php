<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Printaja Vendor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow p-4" style="width: 400px;">
            <h3 class="text-center mb-2">Buat Akun</h3>
            <p class="text-center text-muted mb-4">Buat akun untuk vendor anda</p>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" value="{{ old('nama') }}" required>
                </div>
                <div class="mb-3">
                    <input type="tel" name="nomor_telepon" class="form-control" placeholder="Nomor Telepon" value="{{ old('nomor_telepon') }}"  required  pattern="[0-9]*" inputmode="numeric">
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="confirmpassword" class="form-control" placeholder="Konfirmasi Kata Sandi" 
                        required>
                </div>
                <button typ e="submit" class="btn btn-primary w-100">Buat Akun</button>
            </form>
            @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
            <p class="text-center text-muted mt-3">
                Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a>
            </p>
        </div>
    </div>
</body>

</html>
