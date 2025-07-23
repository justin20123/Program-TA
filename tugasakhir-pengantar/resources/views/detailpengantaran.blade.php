@extends('layout.sneat')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pengantar</li>
    </ol>
@endsection

@section('menu')
    <div class="modal fade" id="modalselesai" tabindex="-1" role="dialog" aria-labelledby="modalKirimContohLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Selesai</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah anda sudah memastikan barang diterima pemesan?</p>
                    <p class="text text-danger">Peringatan: Menyelesaikan pesanan tanpa memastikan sudah diambil pemesan
                        dapat menurunkan rating vendor anda bahkan membuat vendor anda diblokir</p>
                    <button class="btn btn-primary close">Batalkan</button>
                    <button id="btnSelesaiModal" class="btn btn-primary" data-idnota="{{ $nota->id }}">Sudah</button>
                </div>
            </div>
        </div>
    </div>
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
                            <a class="btn btn-primary" href="/{{ $p->url_file }}" target="_blank">Lihat File</a>
                        </div>
                    @endforeach
                </ul>

                <h6 class="fw-bold">Lokasi Pengambilan</h6>
                <a href="https://www.openstreetmap.org/?mlat={{ $nota->latitude_pengambilan }}&mlon={{ $nota->longitude_pengambilan }}&zoom=15&layers=M"
                    target="_blank" class="btn btn-primary">
                    Lihat Pada Peta
                </a>

            </div>
            <button id="btnSelesai" class="btn btn-primary text-center my-5">Selesaikan Pesanan</button>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#btnSelesai').click(function() {

                $('#modalselesai').modal('show');
            });

        });
        $('#btnSelesaiModal').click(function() {
            var idnota = $(this).data('idnota');
            var formData = new FormData();
            formData.append('idnota', idnota);

            $.ajax({
                type: "POST",
                url: "/selesaikanpesanan",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                processData: false,
                contentType: false,
                success: function() {
                    $('#modalselesai').modal('hide');
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        });
    </script>
@endsection
