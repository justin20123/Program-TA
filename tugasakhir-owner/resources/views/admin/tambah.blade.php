@extends ("layout.sneat")
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Vendors</a></li>
        <li class="breadcrumb-item" aria-current="page">Layanan</li>
        <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
        <li class="breadcrumb-item" aria-current="page">Opsi Layanan</li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
@endsection
@section('menu')
    <div class="h3 px-4 py-2 text-center">Tambah Admin</div>
    <form action="{{ route('admin.store') }}" method="post">
        @csrf
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Nama</label>
                <input type="text" class="form-control" name="nama" aria-describedby="helpId"
                    placeholder="" required>
            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" class="form-control" name="email" aria-describedby="helpId"
                    placeholder="" required>
            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Nomor Telepon</label>
                <input type="tel" name="nomor_telepon" class="form-control" placeholder="Nomor Telepon"
                    value="{{ old('nomor_telepon') }}" required pattern="[0-9]*" inputmode="numeric">

            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password" aria-describedby="helpId"
                        placeholder="Enter your password" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" required>
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="password1">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="confirmpassword" id="password1"
                        aria-describedby="helpId" placeholder="Enter your password" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword1">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
            <input type="submit" value="Submit" class="btn btn-success">
        </div>
    </form>
    @if (session('error'))
        <p class="text text-danger">{{ session('error') }}</p>
    @endif
@endsection

@section('script')

<script src = "https://code.jquery.com/jquery-3.6.0.min.js" ></script>
<script>
    $(document).ready(function() {
    // Select all elements whose ID starts with 'togglePassword'
    $('[id^="togglePassword"]').each(function() {
    $(this).on('click', function() {
    // Get the number from the button's ID
    var num = $(this).attr('id').replace('togglePassword', '');

    // Find the corresponding password input
    var passwordInput = $('#password' + num);
    var icon = $(this).find('i');

    if (passwordInput.attr('type') === 'password') {
    passwordInput.attr('type', 'text');
    icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
    passwordInput.attr('type', 'password');
    icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
    });
    });
    });
    </script>
@endsection
