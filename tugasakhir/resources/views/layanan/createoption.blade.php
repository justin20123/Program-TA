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
@section('menu')
<div class="h3 px-4 py-2">Tambah Opsi</div>
<form action="{{ route('opsidetail.create') }}" method="post">
  @csrf
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Opsi</label>
      <input type="text"
        class="form-control" name="opsi" id="" aria-describedby="helpId" placeholder="">
    </div>
  </div>
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Tambahan Biaya</label>
      <input type="number"
        class="form-control" name="biaya_tambahan" id="" aria-describedby="helpId" placeholder="" value="0" min="0">
    </div>
</div>

<input type="hidden" name="idvendor" value="{{ $layanan['idvendor'] }}">
<input type="hidden" name="idlayanan" value="{{ $layanan['idlayanan'] }}">
<input type="hidden" name="iddetail" value="{{ $layanan['iddetail'] }}">

<input type="submit" value="Submit" class="btn btn-success"> 
</form>
@endsection