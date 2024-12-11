@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
@endsection
@section('menu')
    <div class="container my-5">
        <!-- Cart Table -->
        <h4>Cart Vendor A</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"></th>
                    <th>File</th>
                    <th>Products</th>
                    <th>Price (IDR)</th>
                    <th>Quantity</th>
                    <th>Additional Fee</th>
                    <th>Sub-total (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pemesanans as $p)
                    <tr>
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
                                    <a href="#" class="text-primary">Details</a>
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
                    </tr>
                    <input type="hidden" name="biaya_tambahan-{{$p->id}}" id="biaya_tambahan-{{$p->id}}" value="{{$p->biaya_tambahan}}">
                    <input type="hidden" name="jumlah-{{$p->id}}" id="jumlah-{{$p->id}}" value="{{$p->jumlah}}">
                @endforeach


            </tbody>
        </table>

        <!-- Cart Buttons -->
        <div class="d-flex justify-content-between mb-4">
            <a href="/vendor" class="btn btn-outline-primary">Lihat vendor lainnya</a>
            <a href="/vendor/{{ $pemesanans[0]->vendors_id }}" class="btn btn-primary">Tambah Barang</a>
        </div>

        <!-- Balance and Checkout Summary -->
        <div class="d-flex justify-content-between align-items-start">
            <!-- Balance -->
            <h5>Balance: Rp. 500,000</h5>

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
                        <input type="submit" value="UPDATE CHECKOUT" class="btn btn-primary btn-block mt-3 font-weight-bold"> 
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

                        $('#hiddens').append(`<input type="hidden" name="idpemesanans[]" value="${itemId}">`);
                        $('#hiddens').append(`<input type="hidden" name="biaya_tambahan[]" value="${biayaTambahan}">`);
                    }
                });
                $('#subtotal').text('Rp. ' + subtotal.toLocaleString());
                $('#hiddens').append(`<input type="hidden" name="subtotal" value="${subtotal}">`);

            }

            updateSubtotal();

            $('[id^="item-"]').change(function() {
                updateSubtotal(this);
            });

        });
    </script>
@endsection
