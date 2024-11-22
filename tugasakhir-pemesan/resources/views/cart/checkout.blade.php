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
                    <!-- User Name -->
                    <div class="form-group">
                        <label for="username" class="form-label">User Name</label>
                        <input type="text" class="form-control form-input" id="username" placeholder="John Doe"
                            required>
                    </div>
                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control form-input" id="phone" placeholder="Phone Number"
                            required>
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-input" id="email" placeholder="Email">
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="statusantar">
                        <label class="form-check-label custom-radio-label" for="statusantar">Pengantaran</label>
                    </div>

                    <!-- Delivery Option -->
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
            </div>

            <!-- Order Summary Section -->
            <div class="col-md-4">
                <div class="order-summary-container border rounded p-3">
                    <h5 class="order-summary-title mb-3">Order Summary</h5>
                    <!-- Order Items -->
                    @foreach ($pemesanans as $p)
                        <div class="order-item d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <iframe src="/{{ $p->url_file }}"
                                    style="width: 50px; height: 50px; border: none; overflow: hidden;"></iframe>
                                <br>
                                <a class="btn btn-primary" href="/{{ $p->url_file }}" target="_blank">Preview</a>
                            </div>
                            <div class="item-details">
                                <span class="item-name">{{ $p->layanan }}</span>
                                <br>
                                <span class="item-description">{{ $p->jumlah }} {{ $p->satuan }} x Rp.
                                    {{ number_format($p->harga_satuan, 0, '.', ',') }}</span>
                            </div>
                            <span class="item-price">Rp.
                                {{ number_format($p->jumlah * $p->harga_satuan, 0, '.', ',') }}</span>
                        </div>

                        <input type="hidden" name="idpemesanan-{{$p->id}}" id="idpemesanan-{{$p->id}}" value="{{$p->id}}">
                    @endforeach


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

                    console.log("Returned value:", response);
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

                    // Check the state of the checkbox with ID 'statusantar'


                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", xhr);
                }
            });
        }

        async function updateTotal(subtotal) {
            
        }

        $(document).ready(function() {
            let longitude;
            let latitude;
            let biayaAntar;
            let totalBiaya;

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
              let idpemesanans =[];
              let opsiantar;

              const formData = new FormData();

              var subtotalString = $('#subtotal').html();
              var subtotal = parseInt(subtotalString.replace(/\,/g, ''), 10);

              if ($('#statusantar').is(':checked')) {
                totalBiaya = subtotal + biayaAntar;
            } else {
                totalBiaya = subtotal;
            }


              formData.append('harga_total', totalBiaya);

              $('[id^="idpemesanan-"]').each(function() {
                var value = $(this).val();
                idpemesanans.push(value);
              });


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

                formData.append('idpemesanans',idpemesanans);
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
                    error: function(xhr, status, error) {
                        console.error("Error occurred:", xhr);
                    }
                });
            });

            $("#statusantar").on('change', function() {
            updateTotal(parseInt($('#subtotal').html().replace(/\,/g, ''), 10));
                if ($(this).is(':checked')) {
                    $('.delivery').show();
                    
                } else {
                    $('.delivery').hide();
                    
                }
            });
        });
    </script>
@endsection
