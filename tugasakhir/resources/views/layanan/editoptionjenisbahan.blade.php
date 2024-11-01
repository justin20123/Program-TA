@extends ("layout.sneat")
@section ("breadcrumb")
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Jenis Bahan</li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
@endsection
@section("menu")
<form action="{{ route('jenisbahan.update', $jenisbahan->id) }}" method="post">
  @csrf
  @method('PUT')
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Nama jenis bahan</label>
      <input type="text"
        class="form-control" name="nama" id="" aria-describedby="helpId" placeholder="" value="{{$jenisbahan->nama}}">
    </div>
  </div>
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Deskripsi</label>
      <input type="text"
        class="form-control" name="deskripsi" id="" aria-describedby="helpId" placeholder="" value="{{$jenisbahan->deskripsi}}">
    </div>
  </div>

<input type="hidden" name="idvendor" value="{{ $layanan['idvendor'] }}">
<input type="hidden" name="idlayanan" value="{{ $layanan['idlayanan'] }}">

<input type="submit" value="Submit" class="btn btn-success"> 
</form>
@endsection