@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
@endsection
@section('menu')
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tutupmodal">Batalkan</button>
                    <form id="deleteForm" action="/deletepesanan" method="POST">
                        @csrf
                        @method('DELETE')
                        <div id="hiddens-modal">

                        </div>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
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
    <div class="container my-5">
        <!-- Cart Table -->
        <h4 class="mb-3">Keranjang Vendor A</h4>
        <div class="table-responsive-md" style="overflow-x: auto; overflow-y: hidden;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">Pilih</th>
                        <th>File</th>
                        <th>Produk</th>
                        <th>Harga (Rupiah)</th>
                        <th>Kuantitas</th>
                        <th>Biaya Tambahan</th>
                        <th>Sub-total (Rupiah)</th>
                        <th>Catatan</th>
                        <th>Hapus Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pemesanans as $p)
                        <tr>
                            @if($p->status == 'deleted')
                             <td colspan="9">
                                    <div class="alert alert-warning" role="alert">
                                        <p class="p">Jenis bahan {{$p->nama_jenis_bahan}} untuk pesanan {{ $p->layanan }} - {{ $p->jumlah }} {{$p->satuan}} sudah tidak tersedia!</p>
                                        <a href="/editpesanan/{{ $p->id }}" class="btn btn-link">Klik untuk ubah pesanan</a>
                                    </div>
                                </td>
                            
                            @elseif($p->status == 'updated')
                            <td colspan="9">
                                <div class="alert alert-warning" role="alert">
                                    <p class="p">Terdapat perubahan pada jenis bahan {{$p->nama_jenis_bahan}} untuk pesanan {{ $p->layanan }} - {{ $p->jumlah }} {{$p->satuan}}!</p>
                                    <a href="/editpesanan/{{ $p->id }}" class="btn btn-link">Klik untuk ubah pesanan</a>
                                </div>
                            </td>
                            @else
                            <td>
                                <input type="checkbox" id="item-{{ $p->id }}" value="{{ $p->id }}" checked />
                            </td>

                            <td>
                                <div>
                                    <iframe src="/{{ $p->url_file }}"
                                        style="width: 100px; height: 100px; border: none; overflow: hidden;"></iframe>
                                    <br>
                                    <a class="btn btn-primary" href="/{{ $p->url_file }}" target="_blank">Preview</a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">

                                    <div>
                                        {{ $p->layanan }}<br>
                                        <a href="/editpesanan/{{ $p->id }}" class="text-primary">Edit</a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($p->harga_satuan, 0, '.', ',') }}</td>

                            <td>{{ number_format($p->jumlah, 0, '.', ',') }} {{ $p->satuan }}</td>
                            <td>Rp.{{ number_format($p->biaya_tambahan, 0, '.', ',') }}</td>
                            <td class="font-weight-bold">
                                <div id='hargaitem-{{ $p->id }}'>
                                    {{ number_format($p->subtotal_pesanan, 0, '.', ',') }}
                                </div>

                            </td>
                            <td>
                                <button class="btn btn-link bukacatatan" data-idpemesanan="{{ $p->id }}">Lihat
                                    Catatan
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-danger button-delete" data-id="{{ $p->id }}"
                                    data-layanan="{{ $p->layanan }}" data-jumlah="{{ $p->jumlah }}"
                                    data-satuan="{{ $p->satuan }}" data-idvendor="{{ $p->vendors_id }}">Hapus</button>
                            </td>
                        
                        <input type="hidden" name="biaya_tambahan-{{ $p->id }}"
                            id="biaya_tambahan-{{ $p->id }}" value="{{ $p->biaya_tambahan }}">
                        <input type="hidden" name="jumlah-{{ $p->id }}" id="jumlah-{{ $p->id }}"
                            value="{{ $p->jumlah }}">
                            @endif
                        </tr>
                    @endforeach


                </tbody>
            </table>
        </div>

        @if (session('message'))
            <div class="alert alert-danger">
                {!! nl2br(e(session('message'))) !!}
            </div>
        @endif
        <!-- Cart Buttons -->
        <div class="d-flex justify-content-between mb-4 flex-wrap">

            <a href="/vendor/{{ $pemesanans[0]->vendors_id }}" class="btn btn-primary mb-2">Tambah Barang</a>
        </div>

        <!-- Balance and Checkout Summary -->
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <!-- Balance -->
            <h5 class="mb-2">Balance: Rp. 500,000</h5>

            <!-- Card Totals -->
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h6 class="card-title">Card Totals</h6>
                    <div class="d-flex justify-content-between">
                        <span>Sub-total</span>
                        <span class="font-weight-bold" id='subtotal'>Rp.
                            {{ number_format($subtotal, 0, '.', ',') }}</span>
                    </div>
                    <form action="{{ route('bukacheckout') }}" method="POST">
                        @csrf
                        <div id="hiddens">

                        </div>
                        <input type="submit" value="UPDATE CHECKOUT"
                            class="btn btn-primary btn-block mt-3 font-weight-bold">
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            let subtotal = 0;

            function updateSubtotal() {
                subtotal = 0;
                $('#hiddens').empty();
                $('[id^="item-"]').each(function() {
                    var checkboxId = $(this).attr('id');
                    var checkboxValue = $(this).val();
                    if ($(this).is(':checked')) {
                        var priceString = $('#hargaitem-' + checkboxValue).html();
                        var price = parseInt(priceString.replace(/\,/g, ''), 10);

                        var biayaTambahanString = $('#biaya_tambahan-' + checkboxValue).html();
                        var biayaTambahan = parseInt(biayaTambahanString.replace(/\,/g, ''), 10);

                        var jumlahString = $('#jumlah-' + checkboxValue).html();
                        var jumlah = parseInt(jumlahString.replace(/\,/g, ''), 10);
                        subtotal += price;

                        const itemId = $(this).val();

                        var biayaTambahan = $('#biaya_tambahan-' + itemId).val();

                        $('#hiddens').append(
                            `<input type="hidden" name="idpemesanans[]" value="${itemId}">`);
                        $('#hiddens').append(
                            `<input type="hidden" name="biaya_tambahan[]" value="${biayaTambahan}">`);
                    }
                });
                $('#subtotal').text('Rp. ' + subtotal.toLocaleString());
                $('#hiddens').append(`<input type="hidden" name="subtotal" value="${subtotal}">`);

            }

            updateSubtotal();

            $('[id^="item-"]').change(function() {
                updateSubtotal(this);
            });

            $(".button-delete").on("click", function() {
                const id = $(this).data('id');
                const layanan = $(this).data('layanan');
                const jumlah = $(this).data('jumlah');
                const satuan = $(this).data('satuan');
                const idvendor = $(this).data('idvendor');

                var message = "Apakah anda yakin ingin menghapus pesanan: " + layanan + " " + jumlah + " " +
                    satuan;

                $('#modal-body').html(message);
                var hidden = `<input type="hidden" name="idpemesanan" value="${id}">
                <input type="hidden" name="idvendor" value="${idvendor}">`;
                $('#hiddens-modal').html(hidden);
                $('#confirmDeleteModal').modal('show');
            });

            $('.tutupmodal').on('click', function() {
                $('#confirmDeleteModal').modal('hide');
                $('#modalcatatan').modal('hide');
            });

            $('.bukacatatan').click(function() {
                var idpemesanan = $(this).data('idpemesanan');
                $.ajax({
                    type: 'GET',
                    url: '/lihatcatatan/' + idpemesanan,
                    success: function(data) {
                        var title = "Layanan: " + data.layanan + " " + data.jumlah + " " + data.satuan;
                        var catatan = data.catatan;
                        var text = "";
                        if (!catatan) {
                            text = "Belum ada catatan untuk pesanan ini";
                        } else {
                            text = "Catatan: " + catatan;
                        }

                        $("#modalcatatantitle").text(title);
                        $("#modalcatatantext").text(text);
                        $('#modalcatatan').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                        alert("Terjadi kesalahan saat mengambil catatan. Silakan coba lagi.");
                    }
                });
            });
        });
    </script>
@endsection
</create_file>
