<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Printaja Owner</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>

<div class="modal fade" id="modalappkey" tabindex="-1" aria-labelledby="modalappkeyLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalappkeyLabel">Masukkan Kunci Aplikasi Untuk Daftar</h5>
            </div>

            <form method="post" id="formBukaRegister" action="{{ route('tambahappkey') }}">
                @csrf
                <div class="modal-body">
                    <label for="">Kunci Aplikasi</label>
                    <input type="password" name="appkey">
                </div>

                <!-- Save Button -->


                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="btnRegister">Daftar</button>
                    </div>
                </div>
            </form>
            <!-- Modal Body -->


        </div>
    </div>
</div>



<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow p-4" style="width: 400px;">
            <h3 class="text-center mb-4">Printaja Pemilik</h3>
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
                    <small><button type="button" class="btn btn-link" id="btnopenmodal">Daftar Akun</button></small>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Masuk</button>
            </form>
            @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}" defer></script>
<script>
    $(document).ready(function() {
        $('#btnopenmodal').click(function() {
            $('#modalappkey').modal('show');

        });
        $('#btnRegister').click(function (e) { 
            e.preventDefault();
            $('#formBukaRegister').submit();
            
        });
    });
</script>

</html>
