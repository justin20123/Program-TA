@extends ("layout.sneat")
@section ("breadcrumb")
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Opsi Layanan</li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
@endsection
@section("menu")
<div class="h3 px-4 py-2 text-center">Tambah Pegawai</div>
<form action="{{route('pegawai.store')}}" method="post">
  @csrf
<div class="h3 px-4 py-2">
  <div class="form-group">
    <label for="">Nama</label>
    <input type="text"
      class="form-control" name="nama" id="" aria-describedby="helpId" placeholder="" required>
  </div>
</div>
<div class="h3 px-4 py-2">
  <div class="form-group">
    <label for="">Email</label>
    <input type="email"
      class="form-control" name="email" id="" aria-describedby="helpId" placeholder="" required>
  </div>
</div>
<div class="h3 px-4 py-2">
  <div class="form-group">
    <label for="">Nomor Telepon</label>
    <input type="tel" name="nomor_telepon" class="form-control" placeholder="Nomor Telepon" value="{{ old('nomor_telepon') }}"  required  pattern="[0-9]*" inputmode="numeric">

  </div>
</div>
<div class="h3 px-4 py-2">
  <div class="form-group">
    <label for="password">Password</label>
    <div class="input-group">
      <input type="password" class="form-control" name="password" id="password" aria-describedby="helpId" placeholder="Enter your password" required>
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
      <input type="password" class="form-control" name="confirmpassword" id="password1" aria-describedby="helpId" placeholder="Enter your password" required>
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button" id="togglePassword1">
          <i class="fa fa-eye" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="idvendor" value="{{$vendor[0]}}"> 
<div style="display: flex; justify-content: center;" class="pb-5 pt-2">
  <input type="submit" value="Submit" class="btn btn-success">
</div>
</form>
@if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
@endsection

@section("script")
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all elements whose ID starts with 'togglePassword'
    const toggleButtons = document.querySelectorAll('[id^="togglePassword"]');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the number from the button's ID
            const num = this.id.replace('togglePassword', '');
            
            // Find the corresponding password input
            const passwordInput = document.getElementById('password' + num);
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
  </script>
@endsection