@extends ("layout.sneat")
@section('breadcrumb')
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
<a href="{{ url()->previous() }}" class="px-4 py-2 text-black">
    <i class="fas fa-arrow-left"></i>
</a>
    <div class="h3 px-4 py-2">Tambah Opsi</div>
    <form action="{{ route('opsidetail.create') }}" method="post">
        @csrf
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Opsi</label>
                <input type="text" class="form-control" name="opsi" id="" aria-describedby="helpId"
                    placeholder="">
            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Tambahan Biaya</label>
                <input type="number" class="form-control" name="biaya_tambahan" id="" aria-describedby="helpId"
                    placeholder="" value="0" min="0">
            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="tipe">Tipe</label>
                <select class="form-control" name="tipe" id="tipe">

                    <option value="tambahan">Tambahan (harga ditambahkan pada total)</option>
                    <option value="satuan">Satuan (harga ditambahkan per satuan)</option>
                    <option value="jumlah">Tiap Jumlah (contoh: 1 buku berisi 100 lembar, maka ditambahkan sekali per buku)
                    </option>

                </select>
            </div>
        </div>
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <input type="checkbox" name="khusus">
                <label>
                    <h6>Ubah hanya untuk jenis bahan ini</h6>
                </label>

            </div>
        </div>

        <input type="hidden" name="id_detail" value="{{ $layanan['id_detail'] }}">

        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
            <input type="submit" value="Submit" class="btn btn-success">
        </div>
    </form>
@endsection
