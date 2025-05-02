@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item" aria-current="page">Pesanan</li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pesanan</li>
    </ol>
@endsection
@section('menu')
    <div class="modal fade" id="modalKirimContoh" tabindex="-1" role="dialog" aria-labelledby="modalKirimContohLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKirimContohLabel">Submit Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/kirimcontoh" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="fileperubahan">Upload File</label>
                            <input type="file" class="form-control-file" name="fileperubahan" id="fileperubahan"
                                accept=".pdf, image/jpeg, image/jpg, image/png, image/gif" required>
                        </div>
                        <button class="btn btn-primary close">Batalkan</button>
                        <input type="hidden" name="idpemesanan" id="idpemesananhidden">
                        <input type="submit" value="Submit" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalperubahan" tabindex="-1" role="dialog" aria-labelledby="modalKirimContohLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perubahan</h5>
                </div>
                <div class="modal-body">
                    <div id="detail-perubahan">

                    </div>
                    <button class="btn btn-primary close">Batalkan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalantar" tabindex="-1" role="dialog" aria-labelledby="modalKirimContohLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perubahan</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah anda sudah memastikan semua produk selesai?</p>
                    <button class="btn btn-primary close">Batalkan</button>
                    <form action="/pilihpengantar" method="POST">
                        @csrf
                        <input type="hidden" name="idnota" id="idnota" value="{{ $nota_detail[0]['nota']->id }}">

                        <input type="submit" value="Submit" class="btn btn-primary">
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalambil" tabindex="-1" role="dialog" aria-labelledby="modalKirimContohLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perubahan</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah anda sudah memastikan semua produk selesai?</p>
                    <button class="btn btn-primary close">Batalkan</button>
                    <button id="btnAmbilModal" class="btn btn-primary"
                        data-idnota="{{ $nota_detail[0]['nota']->id }}">Sudah</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalselesai" tabindex="-1" role="dialog" aria-labelledby="modalKirimContohLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perubahan</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah anda sudah memastikan barang diterima pemesan?</p>
                    <p class="text text-danger">Peringatan: Menyelesaikan pesanan tanpa memastikan sudah diambil pemesan
                        dapat menurunkan rating vendor anda bahkan membuat vendor anda diblokir</p>
                    <button class="btn btn-primary close">Batalkan</button>
                    <button id="btnSelesaiModal" class="btn btn-primary"
                        data-idnota="{{ $nota_detail[0]['nota']->id }}">Sudah</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalcatatan" tabindex="-1" role="dialog" aria-labelledby="modalcatatanLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalcatatantitle">Catatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    <p class="modal-body" id="modalcatatantext">Catatan</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tutupmodal">Tutup</button>

                </div>
            </div>
        </div>
    </div>

    <div id="error-message" style="color: red; display: none;"></div>
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <h3 class="text-center p-4">Pesanan Vendor Anda</h1>
        <h5 class="text-center">{{ $nota_detail[0]['nota']->nama }}</h2>
            <div class="stepper">
                <div class="step-item active">
                    <div class="step">
                        <span class="step-number">1</span>
                        <span class="step-title">Pesanan Dibuat</span>
                    </div>
                </div>
                <div class="step-item active">
                    <div class="step">
                        <span class="step-number">2</span>
                        <span class="step-title">Diproses</span>
                    </div>
                </div>
                @if (!$nota_detail[0]['nota']->waktu_tunggu_diambil && !$nota_detail[0]['nota']->waktu_diantar)
                    <div class="step-item">
                    @else
                        <div class="step-item active">
                @endif
                <div class="step">
                    <span class="step-number">3</span>
                    @if ($nota_detail[0]['nota']->opsi_pengambilan == 'diantar')
                        <span class="step-title">Sedang Diantar</span>
                    @elseif ($nota_detail[0]['nota']->opsi_pengambilan == 'diambil')
                        <span class="step-title">Menunggu Diambil</span>
                    @endif
                </div>
            </div>
            @if (!$nota_detail[0]['nota']->waktu_selesai)
                <div class="step-item">
                @else
                    <div class="step-item active">
            @endif
            <div class="step">
                <span class="step-number">4</span>
                <span class="step-title">Selesai</span>
            </div>
            </div>
            </div>
            <div class="container-xxl pb-container" style="width: 85%">
                <div class="progress" style="width: 100%">
                    @if (!$nota_detail[0]['nota']->waktu_tunggu_diambil && !$nota_detail[0]['nota']->waktu_diantar)
                        <div class="progress-bar" style="width: 35%"></div>
                    @elseif (!$nota_detail[0]['nota']->waktu_selesai)
                        <div class="progress-bar" style="width: 65.75%"></div>
                    @else
                        <div class="progress-bar" style="width: 100%"></div>
                    @endif
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Produk</th>
                        <th>Detail dan Catatan</th>
                        @if (!$is_verifikasi_selesai)
                            <th>Aksi</th>
                        @endif


                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota_detail[0]['pemesanans'] as $p)
                        <input type="hidden" name="detail-pesanan-{{ $p->id }}" value="{{ $p->detail_pesanan }}">
                        <tr class="justify-content-between">
                            <td>
                                <div>
                                    <iframe src="/{{ $p->url_file }}"
                                        style="width: 100px; height: 100px; border: none; overflow: hidden;"></iframe>
                                    <br>
                                    <a class="btn btn-primary" href="/{{ $p->url_file }}" target="_blank">Lihat
                                        File</a>
                                </div>
                            </td>
                            <td scope="row">
                                <div>{{ $p->layanan }}</div>
                                <div>{{ $p->jumlah . ' ' . $p->satuan }}</div>
                                <div>Rp. {{ number_format($p->harga_satuan * $p->jumlah, 0, ',', '.') }}</div>
                                <a href="/{{ $p->url_file }}/download" class="btn btn-primary py-2" target="_blank">Unduh
                                    File</a>
                            </td>
                            <td>
                                <button class="btn btn-link bukacatatan" data-idpemesanan="{{ $p->id }}">Lihat
                                    Detail dan Catatan
                                </button>
                            </td>
                            <td>
                                <ul class="list-inline justify-content-between">

                                    @if ($p->perlu_verifikasi == 1)
                                        @if ($p->latest_progress != 'terverifikasi')
                                            <li class="list-inline-item">
                                                <button class="btn btn-primary lihatperubahan"
                                                    data-idpemesanan="{{ $p->id }}">Lihat Perubahan</button>
                                            </li>
                                            <li class="list-inline-item">
                                                <button class="btn btn-primary kirimcontoh"
                                                    data-idpemesanan="{{ $p->id }}">Kirimkan Contoh</button>
                                            </li>
                                        @else
                                            <li class="list-inline-item">
                                                <div class="h6">Pesanan selesai terverifikasi, silahkan diselesaikan
                                                </div>
                                            </li>
                                        @endif
                                    @endif

                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($is_selesai)
                <p class="text-center my-5">Pesanan Sudah Selesai</p>
            @else
                @if($nota_detail[0]['nota']->opsi_pengambilan == 'diambil')
                    @if($is_menunggu_selesai)
                        <button class="btn btn-primary text-center my-5" id="btnSelesai">Selesaikan Pesanan</button>
                    @else
                        @if ($is_verifikasi_selesai)
                            <button class="btn btn-primary text-center my-5" id="btnAmbil">Ajukan Pengambilan</button>
                        @else
                            <button class="btn btn-primary text-center my-5 disabled">Ajukan Pengambilan</button>
                        @endif
                    @endif
                @else
                    @if($is_menunggu_selesai)
                        <p class="text-center my-5">Pesanan sedang diantar dan akan diselesaikan oleh pengantar</p>
                    @else
                        @if ($is_verifikasi_selesai)
                            <button class="btn btn-primary text-center my-5" id="btnAntar">Antar</button>
                        @else
                            <button class="btn btn-primary text-center my-5 disabled">Antar</button>
                        @endif
                    @endif
                @endif
            @endif

        @endsection

        @section('script')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('.download-btn').click(function() {
                        var filename = $(this).data('url');
                        var fileUrl = '/file/' + filename; 
                        alert('File URL: ' + fileUrl);
                        console.log('Fetching URL:', fileUrl); 

                        fetch(fileUrl)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok: ' + response.statusText);
                                }
                                return response.blob(); 
                            })
                            .then(blob => {
                                if (blob.size === 0) {
                                    throw new Error('The file is empty.');
                                }

                                var url = window.URL.createObjectURL(blob);

                                var a = document.createElement('a');
                                a.href = url;
                                a.download = filename; 
                                document.body.appendChild(a);
                                a.click(); 
                                a.remove();
                                window.URL.revokeObjectURL(url); 
                            })
                            .catch(error => {
                                $('#error-message').text('There was a problem with the fetch operation: ' +
                                    error.message).show();
                                console.error('Fetch error:', error);
                            });
                    });

                    $(document).on('click', '.kirimcontoh', function() {
                        var button = $(this);
                        var idpemesanan = button.data('idpemesanan');

                        $('#idpemesananhidden').val(idpemesanan);

                        $('#modalKirimContoh').modal('show');
                    });
                    $(document).on('click', '.lihatperubahan', function() {
                        var idpemesanan = $(this).data('idpemesanan');

                        if (!idpemesanan) {
                            console.error('ID Pemesanan is not found.');
                            return; 
                        }

                        const formData = new FormData();
                        formData.append('idpemesanan', idpemesanan);

                        $.ajax({
                            type: "POST",
                            url: "/lihatperubahan",
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response && response.perubahan) {
                                    $('#detail-perubahan').html('<p>' + response.perubahan + '</p>');
                                    $('#modalperubahan').modal('show');
                                } else {
                                    console.error('Invalid response format:', response);
                                }
                            },
                            error: function(xhr) {
                                $('#detail-perubahan').html(
                                    '<p>Belum ada detail perubahan yang dikirimkan</p>');
                                $('#modalperubahan').modal('show');
                            }
                        });
                    });

                    $('#btnAntar').click(function() {

                        $('#modalantar').modal('show');
                    });

                    $('#btnAntarModal').click(function() {
                        var idnota = $(this).data('idnota');
                        var formData = new FormData();
                        formData.append('idnota', idnota);

                        $.ajax({
                            type: "POST",
                            url: "/pilihpengantar",
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                //redirect
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', error);
                                console.error('Response:', xhr.responseText);
                            }
                        });
                    });

                    $('#btnAmbil').click(function() {

                        $('#modalambil').modal('show');
                    });

                    $('#btnAmbilModal').click(function() {
                        var idnota = $(this).data('idnota');
                        var formData = new FormData();
                        formData.append('idnota', idnota);

                        $.ajax({
                            type: "POST",
                            url: "/requestambil",
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            processData: false,
                            contentType: false,
                            success: function() {
                                $('#modalambil').modal('hide');
                                window.location.reload();

                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', error);
                                console.error('Response:', xhr.responseText);
                            }
                        });
                    });

                    $('#btnSelesai').click(function() {

                        $('#modalselesai').modal('show');
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

                    $('.bukacatatan').click(function() {

                        var idpemesanan = $(this).data('idpemesanan');
                        $.ajax({
                            type: 'GET',
                            url: '/lihatcatatan/' + idpemesanan,
                            success: function(data) {
                                var title = "Layanan: " + data.layanan + " " + data.jumlah + " " + data
                                    .satuan;
                                var catatan = data.catatan;
                                var text = "";
                                if (!catatan) {
                                    text = "Belum ada catatan untuk pesanan ini";
                                } else {
                                    text = "Catatan: " + catatan;
                                }

                                var htmlCatatanDetail = "";

                                var detailPesanan = $(`input[name="detail-pesanan-${idpemesanan}"]`)
                                    .val();
                                var detailItem = detailPesanan.split(';');
                                detailItem.pop();

                                var detailHtml = "Detail: ";
                                for (var i = 0; i < detailItem.length; i++) {
                                    detailHtml += "<p>" + (i + 1) + ". " + detailItem[i] + "</p>";
                                }
                                if (detailItem.length == 0) {
                                    detailHtml += "<p>Tidak ada detail yang diperlukan dalam pesanan ini</p>"
                                }
                                else{
                                    detailHtml += "<br>";
                                }

                                htmlCatatanDetail = detailHtml;
                                htmlCatatanDetail += "<p>Catatan: " + text + "</p>";


                                $("#modalcatatantext").html(detailHtml);

                                $("#modalcatatantitle").text(title);
                                $("#modalcatatantext").html(htmlCatatanDetail);
                                $('#modalcatatan').modal('show');
                            }
                        });

                    });

                    $(".close").click(function() {
                        $('#modalKirimContoh').modal('hide');
                        $('#modalperubahan').modal('hide');
                        $('#modalantar').modal('hide');
                        $('#modalambil').modal('hide');
                        $('#modalcatatan').modal('hide');
                    });
                });
            </script>
        @endsection
