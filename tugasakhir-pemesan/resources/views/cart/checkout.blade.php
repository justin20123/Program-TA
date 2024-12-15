@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
@endsection
@section('menu')
    <div class="modal fade" id="modalmap" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">Select Location</h5>
                </div>
                <div class="modal-body">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="number" class="form-control form-input" name="setlatitude" id="setlatitude">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="number" class="form-control form-input" name="setlongitude" id="setlongitude">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveLocation">Save Location</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <!-- Billing Information Section -->
            <div class="col-md-8">
                <h4 class="form-title">Billing Information</h4>
                <form id="formBuatNota" method="POST">
                    @csrf        

                    <!-- Delivery Option -->
                    @if($adapengantar)
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="statusantar">
                        <label class="form-check-label custom-radio-label" for="statusantar">Pengantaran</label>
                    </div>
                    <div class="form-group delivery">
                        <label for="deliveryOption" class="form-label">Lokasi pengambilan (Diperlukan untuk layanan
                            pengantaran)</label>
                        <input type="text" class="form-control form-input" id="lokasi"
                            placeholder="Lokasi Pengambilan" disabled>
                        <div class="d-flex justify-content-between p-3", style="width: 100%">
                            <button id="bukainputlatlong" type="button" class="btn btn-outline-primary">Input
                                Lokasi</button>
                            <button id="lokasisekarang" type="button" class="btn btn-primary">Gunakan Lokasi
                                Anda</button>
                        </div>
                    </div>
                    @else
                    <p>Saat ini vendor belum menyediakan layanan antar</p>
                    @endif
                    
                    <!-- Additional Notes -->
                    <div class="form-group">
                        <label for="orderNotes" class="form-label">Informasi Tambahan</label>
                        <textarea class="form-control form-input" id="orderNotes" rows="3"
                            placeholder="Notes about your order, e.g. special notes for delivery"></textarea>
                    </div>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="idvendor" id="idvendor" value="{{ $pemesanans[0]->vendors_id }}">
                    <input type="hidden" name="diantar" id="diantar" value="{{ $pemesanans[0]->vendors_id }}">
                </form>
                @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
            </div>

            <!-- Order Summary Section -->
            <div class="col-md-4">
                <div class="order-summary-container border rounded p-3">
                    <h5 class="order-summary-title mb-3">Order Summary</h5>
                    <!-- Order Items -->
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        @foreach ($pemesanans as $p)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-heading-{{ $p->id }}">
                                    <button class="accordion-button collapsed d-flex" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse-{{ $p->id }}"
                                        aria-expanded="false" aria-controls="flush-collapse-{{ $p->id }}">
                                        <span>{{ $p->layanan }}</span>
                                        <span class="me-auto"></span>
                                        <span class="me-auto"></span>
                                        <span class="ms-auto">
                                            <div>Jumlah: {{ $p->jumlah }} {{ $p->satuan }}</div>
                                            <div>Harga Satuan: Rp. {{ number_format($p->harga_satuan, 0, '.', ',') }}</div>
                                            <div>Total Harga: Rp.
                                                {{ number_format($p->harga_satuan * $p->jumlah + $p->biaya_tambahan, 0, '.', ',') }}
                                            </div>
                                        </span>
                                    </button>
                                </h2>
                                <div id="flush-collapse-{{ $p->id }}" class="accordion-collapse collapse"
                                    aria-labelledby="flush-heading-{{ $p->id }}"
                                    data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">

                                        <div>Biaya Tambahan: Rp. {{ number_format($p->biaya_tambahan, 0, '.', ',') }}</div>


                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="idpemesanan-{{ $p->id }}"
                                id="idpemesanan-{{ $p->id }}" value="{{ $p->id }}">
                        @endforeach
                    </div>


                    <hr>
                    <!-- Subtotal and Shipping -->
                    <div class="order-summary-info d-flex justify-content-between">
                        <span>Sub-total</span>
                        <span>Rp. <span id="subtotal">{{ number_format($subtotal, 0, '.', ',') }}</span></span>
                    </div>
                    <div class="delivery">
                        <div class="order-summary-info d-flex justify-content-between">
                            <span>Shipping</span>
                            <span>Rp. <span id="biayaAntar">0</span></span>
                        </div>
                    </div>

                    <hr>
                    <!-- Total -->
                    <div class="order-total d-flex justify-content-between font-weight-bold">
                        <span>Total</span>
                        <span>Rp. <span id="totalBiaya">{{ number_format($subtotal, 0, '.', ',') }}</span></span>
                    </div>

                    <div class="order-total d-flex justify-content-between font-weight-bold">
                        <span>Saldo</span>
                        <span>Rp. <span id="saldo">500.000</span></span>
                    </div>
                    <!-- Place Order Button -->
                    <button class="btn btn-warning btn-block mt-3" id="btnPlaceOrder" type="submit">PLACE ORDER</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let longitude;
        let latitude;
        let biayaAntar;
        let totalBiaya;
        async function ambilLokasiUser() {
            return new Promise((resolve, reject) => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(resolve, reject);
                } else {
                    reject("Geolocation tidak dapat dilakukan melalui browser ini.");
                }
            });
        }

        async function ambilLokasiSekarang() {
            const position = await ambilLokasiUser();
            latitude = position.coords.latitude;
            longitude = position.coords.longitude;

            updateBiayaAntar();

        }

        async function updateLatLong() {
            $("#latitude").val(latitude);
            $("#longitude").val(longitude);


        }

        async function updateBiayaAntar() {
            updateLatLong();
            const idvendor = $('#idvendor').val();
            console.log(latitude, longitude)
            $.ajax({
                type: "POST",
                url: "/getjarak",
                contentType: 'application/json',
                data: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude,
                    idvendor: idvendor,
                    isrounded: true
                }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {

                    var jarak = response;
                    $('#lokasi').val(`Jarak anda dengan vendor: ${jarak} km`);
                    biayaAntar = jarak * 5000;


                    $('#biayaAntar').html(biayaAntar.toLocaleString());
                    var subtotal = $('#subtotal').html();

                    var subtotalString = $('#subtotal').html();
                    var subtotal = parseInt(subtotalString.replace(/\,/g, ''), 10);

                    if ($('#statusantar').is(':checked')) {
                        totalBiaya = subtotal + biayaAntar;
                    } else {
                        totalBiaya = subtotal;
                    }
                    $('#totalBiaya').html(totalBiaya.toLocaleString());




                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", xhr);
                }
            });
        }

        async function updateTotal(subtotal) {

        }

        $(document).ready(function() {


            $(".delivery").hide();
            ambilLokasiSekarang();


            // Open the map when the button is clicked
            $('#bukainputlatlong').click(function() {
                $('#modalmap').modal('show');
            });

            $('#lokasisekarang').click(function() {
                ambilLokasiSekarang();
            });

            $("saveLocation").click(function() {

                latitude = $("#setlatitude").val();
                longitude = $("#setlongitude").val();

                updateBiayaAntar();
            });

            $("#btnPlaceOrder").click(function() {

                let opsiantar;

                updateBiayaAntar();


                const formData = new FormData();

                var subtotalString = $('#subtotal').html();
                var subtotal = parseInt(subtotalString.replace(/\,/g, ''), 10);



                // var biaya =


                if ($('#statusantar').is(':checked')) {
                    totalBiaya = subtotal + biayaAntar;
                    console.log(biayaAntar);
                } else {
                    totalBiaya = subtotal;
                }
                // console.log(totalBiaya);


                formData.append('harga_total', totalBiaya);
                let idpemesanans = '';
                $('[id^="idpemesanan-"]').each(function() {
                    var value = $(this).val();
                    idpemesanans += value + ',';
                });
                if (idpemesanans != '') {
                    idpemesanans = idpemesanans.slice(0, -1);
                }

                // console.log(idpemesanans);


                if ($('#statusantar').is(':checked')) {
                    opsiantar = "diantar";
                    formData.append('latitude', latitude);
                    formData.append('longitude', longitude);
                } else {
                    opsiantar = "diambil";
                    latitude = null;
                    longitude = null;
                }
                formData.append('opsiantar', opsiantar);

                formData.append('idpemesanans', idpemesanans);
                formData.append('catatan_antar', $("#orderNotes").val());


                $.ajax({
                    url: '/placeorder',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            console.log(result);
                            if (result.isConfirmed) {

                                // Redirect to the vendor page
                                window.location.href = '/vendor/1';
                            }
                        });
                    },
                    error: function(xhr, status, error, message) {
                        console.error("Error occurred:", message);
                    }
                });
            });

            $("#statusantar").on('change', function() {
                updateBiayaAntar(); //
                if ($(this).is(':checked')) {
                    $('.delivery').show();

                } else {
                    $('.delivery').hide();

                }
            });
        });
    </script>
@endsection
