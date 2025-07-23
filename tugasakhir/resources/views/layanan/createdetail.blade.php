@extends ("layanan.optiontemplate")
@section ("breadcrumb")
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
    <li class="breadcrumb-item active" aria-current="page">Opsi Layanan</li>
  </ol>
@endsection
@section("menu")
<a href="{{ url()->previous() }}" class="px-4 py-2 text-black">
  <i class="fas fa-arrow-left"></i>
</a>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
<div class="h3 px-4 py-2">Tambah Detail: {{ $jenis_bahan->value }}</div>
<form action="{{ route('detail.store') }}" method="post">
  @csrf
  @method("PUT")
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Nama Detail</label>
      <input type="text" class="form-control" name="value" id="" aria-describedby="helpId" placeholder="">
    </div>
    <div class="form-group">
      <input type="checkbox" name="ubah_semua">
      <label><h6>Tambah untuk semua jenis bahan pada layanan ini</h6></label>
      
    </div>
</div>

<input type="hidden" name="id_vendor" value="{{ $layanan['idvendor'] }}">
<input type="hidden" name="id_layanan" value="{{ $layanan['idlayanan'] }}">
<input type="hidden" name="id_jenis_bahan" value="{{ $layanan['idjenisbahan'] }}">
<div style="display: flex; justify-content: center;" class="pb-5 pt-2">
  
  <input type="submit" value="Tambah" class="btn btn-success">
</div>
</form>

@endsection
