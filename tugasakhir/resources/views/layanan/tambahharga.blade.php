@extends ("layout.sneat")
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Vendors</a></li>
        <li class="breadcrumb-item" aria-current="page">Layanan</li>
        <li class="breadcrumb-item" aria-current="page">Edit Harga</li>
        <li class="breadcrumb-item" aria-current="page">Opsi Harga</li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
@endsection
@section('menu')
    <div class="h3 px-4 py-2">Harga {{ $detail->nama }}</div>
    <form action="{{ route('harga.store') }}" method="post">
        @csrf
        
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Jumlah Minimum</label>
                <input type="number" class="form-control" name="min" id="" aria-describedby="helpId"
                    placeholder="">
            </div>
        </div>
        <div class="h3 px-4 py-2">
          <div class="form-group">
              <label for="">Jumlah Maksimum</label>
              <input type="number" class="form-control" name="max" id="" aria-describedby="helpId"
                  placeholder="">
          </div>
      </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Harga satuan</label>
                <input type="number" class="form-control" name="harga" id="" aria-describedby="helpId"
                    placeholder="">
            </div>
        </div>
        <input type="hidden" name='id_jenis_bahan' value="{{ $detail->id }}">
        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
          <input type="submit" value="Tambah" class="btn btn-success">
        </div>
    </form>
@endsection
