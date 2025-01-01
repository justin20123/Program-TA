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
<form action="{{ route('opsidetail.update', $opsiDetail->id) }}" method="post">
  @csrf
  @method('PUT')
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Opsi</label>
      <input type="text"
        class="form-control" name="opsi" id="" aria-describedby="helpId" placeholder="" value="{{$opsiDetail->opsi}}">
    </div>
  </div>
  <div class="h3 px-4 py-2">
    <div class="form-group">
      <label for="">Tambahan Biaya</label>
      <input type="number"
        class="form-control" name="biaya_tambahan" id="" aria-describedby="helpId" placeholder="" value="{{$opsiDetail->biaya_tambahan}}">
    </div>
</div>

<input type="hidden" name="idvendor" value="{{ $layanan['idvendor'] }}">
<input type="hidden" name="idlayanan" value="{{ $layanan['idlayanan'] }}">
<input type="hidden" name="iddetail" value="{{ $opsiDetail->detail_cetaks_id }}">

<div style="display: flex; justify-content: center;" class="pb-5 pt-2">
  <input type="submit" value="Submit" class="btn btn-success">
</div>    
</form>
@endsection