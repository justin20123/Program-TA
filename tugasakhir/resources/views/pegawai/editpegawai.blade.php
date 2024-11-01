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
<div class="h3 px-4 py-2">Edit pegawai: {{$pegawai->nama}}</div>
<form action="{{ route('pegawai.update', $pegawai->id)}}" method="post">
  @csrf
  @method('PUT')
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Nama</label>
      <input type="text"
        class="form-control" name="nama" id="" aria-describedby="helpId" placeholder="" value="{{$pegawai->nama}}">
    </div>
</div>
<input type="hidden" name="idvendor" value="{{$vendor[0]}}">
<input type="submit" value="Submit" class="btn btn-success"> 
</form>
@endsection