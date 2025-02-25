@extends('layout.sneat')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pengantar</li>
    </ol>
@endsection

@section('menu')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Detail Pengantaran</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Penerima:</label>
                <p class="form-control-plaintext">{{ $nota->nama_pengguna }}</p>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nomor Telepon:</label>
                <p class="form-control-plaintext">{{ $nota->nomor_telepon }}</p>
            </div>

            <h6 class="fw-bold">Pesanan</h6>
            <ul class="list-group mb-3">
                @foreach ($pesanans as $p)
                    <li class="list-group-item">{{ $p->layanan }}: {{ $p->jumlah }} {{ $p->satuan }}</li>
                    <h6 class="fw-bold">Dokumen</h6>
                    <div>
                        <iframe src="/{{ $p->url_file }}"
                            style="width: 100px; height: 100px; border: none; overflow: hidden;"></iframe>
                        <br>
                        <a class="btn btn-primary" href="/{{ $p->url_file }}" target="_blank">Preview</a>
                    </div>
                @endforeach
            </ul>

            <h6 class="fw-bold">Lokasi Pengambilan</h6>
            <a href="https://www.openstreetmap.org/?mlat={{ $nota->latitude_pengambilan }}&mlon={{ $nota->longitude_pengambilan }}&zoom=15&layers=M" target="_blank" class="btn btn-primary">
                Lihat Pada Peta
              </a>
              
        </div>
        <a href="/selesaipesanan/{{$nota->id}}" class="btn btn-primary text-center my-5">Selesaikan Pesanan</a>
    </div>
</div>
@endsection


