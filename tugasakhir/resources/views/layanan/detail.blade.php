@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Vendors</a></li>
        <li class="breadcrumb-item" aria-current="page">Layanan</li>
        <li class="breadcrumb-item active" aria-current="page">Detail Layanan</li>
    </ol>
@endsection
@section('menu')
    <div class="modal fade" id="modalUploadFotoJenisBahan" tabindex="-1" role="dialog"
        aria-labelledby="modalKirimContohLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKirimContohLabel">Kirim Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="uploadjenisbahan" action="/uploadfotojenisbahan" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("PUT")
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="filefoto">Unggah Foto</label>
                            <br>
                            <input type="file" class="form-control-file" name="file_foto" id="file_foto"
                                accept="image/jpeg, image/jpg, image/png, image/gif" required>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary close">Batalkan</button>
                        <div id="hiddenFoto">

                        </div>
                        <button type="button" id="btnsubmitfoto" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="message">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" action="{{ route('jenisbahan.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div id="hiddens">

                        </div>
                        <button type="submit" class="btn btn-danger" id="btndelete">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="messagedetail">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteDetailForm" action="{{ route('detail.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div id="hiddensdetail">

                        </div>
                        <button type="submit" class="btn btn-danger" id="btndeletedetail">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editJenisBahanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateForm" action="{{ route('jenisbahan.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="h3 px-4 py-2">
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama" id="inputnama"
                                    aria-describedby="helpId" placeholder=""
                                    value="{{ $jenis_bahan[0]->nama_jenis_bahan }}">
                            </div>
                        </div>
                        <div class="h3 px-4 py-2">
                            <label class="font-weight-bold">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsistore" class="form-control" rows="3">{{ $jenis_bahan[0]->deskripsi }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>



                        <div id="hiddenEdit">

                        </div>
                        <button type="submit" class="btn btn-primary" id="btnedit">Edit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tambahJenisBahanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createForm" action="{{ route('jenisbahan.store') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="h3 px-4 py-2">
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama" id=""
                                    aria-describedby="helpId" placeholder="">
                            </div>
                        </div>
                        <div class="h3 px-4 py-2">
                            <label class="font-weight-bold">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <input type="hidden" name="id_vendor" value="{{ $jenis_bahan[0]->id_vendor }}">
                        <input type="hidden" name="id_layanan" value="{{ $jenis_bahan[0]->id_layanan }}">


                        <button type="submit" class="btn btn-primary" id="btntambah">Tambah</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="/" class="px-4 pt-3 text-black">
        <i class="fas fa-arrow-left"></i>
    </a>
    <ul class="list-inline p-3">
        <li class="list-inline-item h2">{{ $layanan['nama_layanan'] }}</li>
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <li class="list-inline-item">
            <div class="rating-images">
                @for ($i = 0; $i < 5; $i++)
                    @if ($i < round($layanan['rating']))
                        <img class="img-fluid" src="{{ asset('../assets/images/rating.png') }}" alt="">
                    @else
                        <img style="opacity: 0.5;" class="img-fluid" src="{{ asset('../assets/images/rating.png') }}"
                            alt="">
                    @endif
                @endfor
            </div>
        </li>
        <li class="list-inline-item h6">({{ $layanan['total_nota'] }})</li>
    </ul>
    <a class="px-4" href="/ulasan/{{ $jenis_bahan[0]->id_vendor }}/layanan/{{ $jenis_bahan[0]->id_layanan }}">Lihat
        ulasan</a>
    <div class="form-group px-4 py-2 col-sm-5">
        <label for="pilihanjenisbahan">Pilihan dan Jenis Barang</label>
        <span>
            <div class="select-container">
                <select class="form-control custom-select" name="pilihanjenisbahan" id="pilihanjenisbahan">
                    @foreach ($jenis_bahan as $j)
                        <option value="{{ $j->id_jenis_bahan }}">{{ $j->nama_jenis_bahan }}</option>
                    @endforeach
                </select>
                <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
            </div>
        </span>
        <span>


        </span>
        <div class="py-2" id="jenis-bahan-actions">
            <div class="row">
                <div class="col-sm-4">
                    <button id="btnopentambah" class="btn btn-primary">
                        Tambah Jenis Bahan
                    </button>
                </div>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary btnopenedit" data-toggle="modal"
                        data-id="{{ $jenis_bahan[0]->id_jenis_bahan }}">
                        Ubah Jenis Bahan
                    </button>
                </div>
                <div class="col-sm-4" id="opendelete">
                    <button type="button" class="btn btn-danger btnopendelete" data-toggle="modal"
                        data-id="{{ $jenis_bahan[0]->id_jenis_bahan }}"
                        data-nama="{{ $jenis_bahan[0]->nama_jenis_bahan }}">
                        Hapus Jenis Bahan
                    </button>
                </div>
            </div>
        </div>
        <div id="gambar">

            <button id="btnopenupload" class="btn btn-primary my-2 btnopenupload"
                data-id="{{ $jenis_bahan[0]->id_jenis_bahan }}">
                Unggah Foto Jenis Bahan
            </button>

        </div>
        <div id="editHarga">
            <a href="/layanancetak/editharga/{{ $jenis_bahan[0]->id_jenis_bahan }}" class="btn btn-primary">Ubah
                Harga</a>
        </div>
    </div>
    <h5 class="h5 mx-4">Daftar Detil</h5>
    <div id="listOpsi">
        @foreach ($opsi_detail as $od)
            <div class="form-group px-4 py-2 col-sm-5">
                <label id="detail-{{ $od['detail']->id }}">{{ $od['detail']->value }}</label>
                <a href="/opsidetail/list/{{ $od['detail']->id }}" class="btn btn-primary btn-sm mx-2">Lihat Opsi</a>
                <button class="btn btn-danger btn-sm mx-2 btnopendeletedetail" data-id="{{ $od['detail']->id }}"
                    data-value="{{ $od['detail']->value }}">Hapus Opsi</button>
            </div>
        @endforeach
        @if (count($opsi_detail) == 0)
            <div class="form-group px-4 py-2 col-sm-5">
                <p>Belum ada detil tersedia</p>
            </div>
        @endif
    </div>

    <div class="px-4 py-2 d-flex justify-content-center">
        <div id="tambahdetail">
            <a href="/layanancetak/detail/{{ $jenis_bahan[0]->id_jenis_bahan }}/create" class="btn btn-primary">Tambah
                Detil</a>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#pilihanjenisbahan').on('change', function() {
                updateOpsiDetail({{ $jenis_bahan[0]->id_vendor }}, {{ $jenis_bahan[0]->id_layanan }}, $(
                    this).val());
            });

            function updateOpsiDetail(idvendor, idlayanan, idjenisbahan) {

                $.ajax({
                    url: `/layanans/${idvendor}/details/${idlayanan}/${idjenisbahan}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#listOpsi').empty();
                        if (response.result !== 'success' || !Array.isArray(response.data)) {
                            $('#listOpsi').html('<p>No Options available.</p>');
                            return;
                        }

                        // Create an empty string to hold the HTML
                        let html = '';

                        // Use jQuery's each to iterate over the response data
                        $.each(response.data, function(index, item) {
                            let detail = item.detail;


                            html += `
                                <div class="form-group px-4 py-2 col-sm-5">
                                    <label id="detail-${detail.id}">${detail.value}</label>
                                    <a href="/opsidetail/list/${detail.id}" class="btn btn-primary btn-sm mx-2">Lihat Opsi</a>
                                    <button class="btn btn-danger btn-sm mx-2 btnopendeletedetail" data-id="${detail.id}" data-value="${detail.value}">Hapus Opsi</button>
                                </div>
                            `;
                        });

                        // Append the constructed HTML to #listOpsi
                        $('#listOpsi').html(html);
                        $('#editHarga').html(
                            `<a href="/layanancetak/editharga/${idjenisbahan}" class="btn btn-primary">Ubah Harga</a>`
                        );
                        $('#tambahdetail').html(
                            `<a href="/layanancetak/detail/${idjenisbahan}/create" class="btn btn-primary">Tambah Detil</a>`
                        );
                        $('#opendelete').html(`
                            <button type="button" class="btn btn-danger btnopendelete" data-toggle="modal"
                            data-id="${idjenisbahan}" data-nama="${response.jenis_bahan.nama}">
                            Hapus Jenis Bahan
                            </button>
                        `);
                        $('#inputnama').val(response.jenis_bahan.nama);
                        $('#deskripsistore').val(response.jenis_bahan.deskripsi);
                        $('#gambar').html(`
                        <button id="btnopenupload" class="btn btn-primary my-2 btnopenupload" data-id="${idjenisbahan}">
                            Unggah Foto Jenis Bahan
                        </button>
                        `);
                    },
                    error: function() {
                        console.error('Error fetching opsi details');
                    }
                });
            }

            $(document).on('click', '.btnopenupload', function() {
                var id = $(this).data('id');
                $('#hiddenFoto').html(`
                    <input type="hidden" name="id_jenis_bahan" value="${id}">
                `);
                $('#modalUploadFotoJenisBahan').modal('show');
            });
            $(document).on('click', '.btnopendeletedetail', function() {
                var id = $(this).data('id');
                var value = $(this).data('value');
                $('#messagedetail').html(`Apakah anda yakin ingin menghapus detail ${value}?`);
                $('#hiddensdetail').html(`
                    <input type="hidden" name="id_detail" value="${id}">
                `);
                $('#confirmDeleteDetailModal').modal('show');
            });

            $(document).on('click', '.btnopendelete', function() {
                var id = $('#pilihanjenisbahan').val();
                var nama = $(this).data('nama');
                $('#message').html(`Apakah anda yakin ingin menghapus jenis bahan: ${nama}`);
                $('#hiddens').html(`
                    <input type="hidden" name="id_jenis_bahan" value="${id}">
                `);
                $('#confirmDeleteModal').modal('show');
            });

            $(document).on('click', '.btnopenedit', function() {
                var id = $('#pilihanjenisbahan').val();
                $('#hiddenEdit').html(`
                    <input type="hidden" name="id_jenis_bahan" value="${id}">
                `);

                $('#editJenisBahanModal').modal('show');
            });

            $('#btnopentambah').click(function() {
                $('#tambahJenisBahanModal').modal('show');

            });
            $('#btnsubmitfoto').click(function() {
                $('#uploadjenisbahan').submit();
            });
            $('#btndelete').click(function() {
                $('#deleteForm').submit();
            });
            $('#btndeletedetail').click(function() {
                $('#deleteDetailForm').submit();
            });
            $('#btnupdate').click(function() {
                $('#updateForm').submit();
            });
            $('#btncreate').click(function() {
                $('#createForm').submit();
            });
        });
    </script>
@endsection
