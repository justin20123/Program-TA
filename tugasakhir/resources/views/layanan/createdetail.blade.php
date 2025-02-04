@extends ("layanan.optiontemplate")
@section ("breadcrumb")
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
    <li class="breadcrumb-item active" aria-current="page">Opsi Layanan</li>
  </ol>
@endsection
@section("menu")
<a href="{{ url()->previous() }}" class="px-4 py-2 text-black">
  <i class="fas fa-arrow-left"></i>
</a>
<div class="h3 px-4 py-2">Tambah Opsi: {{ $detail->value }}</div>
<form action="{{ route('detail.store') }}" method="post">
  @csrf
  @method("PUT")
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Nama Detail</label>
      <input type="text" class="form-control" name="value" id="" aria-describedby="helpId" placeholder="">
    </div>
    <div class="form-group">
      <input type="checkbox" name="khusus">
      <label><h6>Tambah hanya untuk jenis bahan ini</h6></label>
      
    </div>
</div>

<input type="hidden" name="idvendor" value="{{ $layanan['idvendor'] }}">
<input type="hidden" name="idlayanan" value="{{ $layanan['idlayanan'] }}">
<input type="hidden" name="idjenisbahan" value="{{ $layanan['idjenisbahan'] }}">
<div style="display: flex; justify-content: center;" class="pb-5 pt-2">
  
  <input type="submit" value="Submit" class="btn btn-success">
</div>
</form>

@endsection
