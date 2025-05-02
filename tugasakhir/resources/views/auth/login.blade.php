<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Printaja Vendor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
    </style>
</head>

<body class="bg-light">
   
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow p-4" style="width: 400px;">
            <h3 class="text-center mb-4">Printaja Vendor</h3>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Nama Pengguna</label>
                    <input type="text" name="email" id="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between">
                    <small><a href="{{ route('register') }}">Daftar Sekarang</a></small>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Masuk</button>
            </form>
            @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
        </div>
    </div>

</body>

</html>
