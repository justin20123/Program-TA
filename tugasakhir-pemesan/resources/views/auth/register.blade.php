<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .register-container {
            display: flex;
            height: 100%; /* Full height of the viewport */
            width: 100%;
        }
        .register-form {
            flex: 40; /* Form takes 40% of the width */
            padding: 3rem;
            background-color: white;
            display: flex; /* Use Flexbox */
            flex-direction: column; /* Stack children vertically */
            align-items: center; /* Center children horizontally */
            justify-content: center; /* Center children vertically */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for better visibility */
        }
        .register-image {
            flex: 60;
            background: url('{{ asset('assets/img/downloads/sign_up.png') }}') center center/cover no-repeat;
        }
        .form-control {
            width: 100%; 
            margin: 0; 
        }
    </style>
</head>
<body>
   

    <div class="register-container">
        <div class="register-form">
            <h2 class="text-center mb-4">Buat Akun</h2>

            <p class="text-center">Membutuhkan beberapa detik untuk membuat akun.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>

                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}"  required  pattern="[0-9]*" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label>Alamat Email</label>

                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>

                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>

                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">Saya telah membaca dan menerima Syarat dan Ketentuan</label>

                </div>
                <button type="submit" class="btn btn-primary btn-block">Buat Akun</button>

                <p class="text-center mt-3">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>

                </p>
            </form>
            @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
        </div>
        <div class="register-image"></div>
    </div>

</body>
</html>
