@extends ("layout.sneat")
@section ("breadcrumb")
@endsection
@section("menu")
<div class="h3 px-4 py-2 text-center">Tarik Dana (simulasi)</div>
<form action="{{route('dotarikdana')}}" method="post">
  @csrf

<div class="h3 px-4 py-2">
  <div class="form-group">
    <label for="">Nomor Rekening</label>
    <input type="number"
      class="form-control" name="norek" id="" aria-describedby="helpId" placeholder="" required>
  </div>
</div>
<div class="h3 px-4 py-2">
  <div class="form-group">
    <label for="">Nominal</label>
    <input type="text"
      class="form-control" name="nominal" id="" aria-describedby="helpId" placeholder="" required>
  </div>
</div>
<div style="display: flex; justify-content: center;" class="pb-5 pt-2 py-2">
  <input type="submit" value="Tarik" class="btn btn-success">
</div>
</form>
@if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
@endsection
