@extends ("layout.sneat")
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item" aria-current="page">Layanan</li>
        <li class="breadcrumb-item" aria-current="page">Edit Harga</li>
        <li class="breadcrumb-item" aria-current="page">Opsi Harga</li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
@endsection
@section('menu')
    <div class="h3 px-4 py-2">Harga {{ $detail->nama }}</div>
    <form action="{{ route('harga.update', $harga->id) }}" method="post">
        @csrf
        
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Jumlah Minimum</label>
                <input type="number" class="form-control" name="min" id="" aria-describedby="helpId"
                    placeholder="" value="{{ $harga->jumlah_cetak_minimum }}">
            </div>
        </div>
        <div class="h3 px-4 py-2">
          <div class="form-group">
              <label for="">Jumlah Maksimum</label>
              <input type="number" class="form-control" name="max" id="" aria-describedby="helpId"
                  placeholder="" value="{{ $harga->jumlah_cetak_maksimum }}">
          </div>
      </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Harga satuan</label>
                <input type="number" class="form-control" name="harga" id="" aria-describedby="helpId"
                    placeholder="" value="{{ $harga->harga_satuan }}">
            </div>
        </div>
        <input type="hidden" name='id_jenis_bahan' value="{{ $detail->id }}">
        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
          <input type="submit" value="Submit" class="btn btn-success">
        </div>
    </form>
@endsection
