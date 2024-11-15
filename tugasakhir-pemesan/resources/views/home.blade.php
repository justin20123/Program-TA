@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
    </ol>
@endsection
@section('menu')
    <div id="loading">
        <div class="d-flex align-items-center justify-content-center" style="height: 100vh; display: flex;">
            <strong>Loading...</strong>
            <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
        </div>
    </div>
    
    <div id="content">
        <div class="container mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Welcome customer</h5>
                <div class="form-inline">
                    <label class="mr-2">Perbandingan Harga:</label>
                    <div class="input-group">
                        <div class="select-container">
                            <select class="form-control custom-select px-4" name="layanans" id="layanans">
                                <option value="">Fotokopi</option>
                            </select>
                            <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="layanan-terdekat" class="p-5"></div>
        <div id="untuk-anda" class='p-5'></div>
        <div id="vendors-terdekat" class="p-5"></div>
    </div>


    <p id="status" class="px-5"></p>
@endsection

@section('script')
    <script>
        var isLoaded = false;
        //ambil lokasi sekarang
        function ambilLokasiUser() {
            return new Promise((resolve, reject) => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(resolve, reject);
                } else {
                    reject("Geolocation tidak dapat dilakukan melalui browseer ini.");
                }
            });
        }

        function getDataLayanan() {
            return $.ajax({
                url: 'getLayanan',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                }
            });
        }
        //kirim lokasi ke controller
        function kirimLokasi(latitude, longitude) {
            return $.ajax({
                url: 'location',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    latitude,
                    longitude
                }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                }
            });
        }

        function ambilDataVendorTerdekat(data) {
            let html = "";
            $('#vendors-terdekat').html(`
            <div class="d-flex justify-content-between align-items-center px-4 pt-3">
                <div class="h5 p-2">List Vendor</div>
                <a href="#" class="text-danger h5 p-2">Lihat Semua</a></div>
                <div class="container mt-5">
                <div class="row" id="vendorsJarak">
                </div>
            </div>
        `);
            $.each(data['data'], function(index, item) {
                html += `
                <div class="col-md-3">
                    <button class="btn-card border-0" onclick="toVendorData(`+ item['id'] +`)">
                        <div class="card" style='width:18rem; height:24rem;'>
                            <img style='width:18rem; height:12rem;' src="` + item['foto_lokasi'] + `" class="card-img-top" alt="Card image 1">
                            <div class="card-body">
                                <h6 class="card-title">` + item['nama'] + `</h6>
                                <p class="card-text">` + item['jarak'] + ` km dari lokasi anda</p>
                                <a href="https://www.openstreetmap.org/?mlat=` + item['latitude'] + `&mlon=` + item[
                            'longitude'] + `#map=15/` + item['latitude'] + `/` + item['longitude'] + `" target="_blank">Lihat di map</a>
                            </div>
                        </div>
                    </button>
                </div>
            `;
            });
            $('#vendorsJarak').html(html);
        }

        function ambilDataLayananTerdekat(data) {
            let html = "";
            $('#layanan-terdekat').html(`
            <div class="d-flex justify-content-between align-items-center px-4 pt-3">
                <div class="h5 p-2">Jenis Layanan</div>
                <a href="#" class="text-danger h5 p-2">Lihat Semua</a></div>
                <div class="container mt-5">
                <div class="row" id="layananJarak">
                </div>
            </div>
        `);
            $.each(data['data'], function(index, item) {
                html += `
                
                    <div class="col-md-3">
                        <button class="btn-card border-0" onclick="toVendorData(`+ item['id'] +`, 'layanan', `+ item['idlayanan'] +`)">
                            <div class="card" style='width:18rem; height:24rem;'>
                                <img style='width:18rem; height:12rem;' src="` + item['foto_lokasi'] + `" class="card-img-top" alt="Card image 1">
                                <div class="card-body">
                                    <h6 class="card-title">` + item['layanan'] + `</h6>
                                    <p class="card-text">` + item['nama'] + `</p>
                                    <p class="card-text">Terdekat: ` + item['jarak'] + ` km dari lokasi anda</p>
                                    <a href="https://www.openstreetmap.org/?mlat=` + item['latitude'] + `&mlon=` + item[
                                'longitude'] + `#map=15/` + item['latitude'] + `/` + item['longitude'] + `" target="_blank">Lihat di map</a>
                                </div>
                            </div>
                        </a>
                    </div>
                
            `;
            });
            $('#layananJarak').html(html);
        }

        function kirimReqUntukAnda(latitude, longitude, idlayanan) {
            return $.ajax({
                url: 'untukanda',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude,
                    idlayanan: idlayanan
                }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                }
            });
        }

        function kirimLayananTerdekat(latitude, longitude) {
            return $.ajax({
                url: 'layananterdekat',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                }
            });
        }
        async function pageLoadDropDownLayanan() {
            $('#status').text("Loading Data...");

            try {

                // Send the location to the backend
                const layananResponse = await getDataLayanan();
                let html = "";
                $.each(layananResponse['data'], function(index, item) {
                    html += `
                <option value='` + item['id'] + `'>` + item['nama'] + `</option>
                `;
                });
                $('#layanans').html(html);

                $('#status').text(''); // Clear loading message
                return "done";

            } catch (error) {
                $('#status').text(error);
                console.error('Error:', error);
                return "error";

            }
        }

        async function pageLoadLayananTerdekat(latitude, longitude) {
            $('#status').text("Loading Data...");

            try {

                // Send the location to the backend
                const layananResponse = await kirimLayananTerdekat(latitude, longitude);
                ambilDataLayananTerdekat(layananResponse);

                $('#status').text(''); // Clear loading message
                return "done";

            } catch (error) {
                $('#status').text(error);
                console.error('Error:', error);
                return "error";

            }
        }
        async function pageLoadTerdekat(latitude, longitude) {
            $('#status').text("Loading Data...");

            try {

                // Send the location to the backend
                const locationResponse = await kirimLokasi(latitude, longitude);
                ambilDataVendorTerdekat(locationResponse);

                $('#status').text(''); // Clear loading message
                return "done";

            } catch (error) {
                $('#status').text(error);
                console.error('Error:', error);
                return "error";

            }
        }

        async function pageLoadUntukAnda(latitude, longitude, idlayanan) {
            try {
                const response = await kirimReqUntukAnda(latitude, longitude, idlayanan);
                // console.log(response);
                data = response['data']
                let layanan = data['layanan'];
                let satuan = data['satuan'];
                let vendors = data['vendors'];
                let html = `<div class="h5 p-4 pb-2">Untuk Anda</div>
                     <hr>`
                let items = ['Terdekat', 'Termurah', 'Direkomendasikan']
                for (let i = 0; i < items.length; i++) {
                    console.table(vendors);
                    html += `
                    <button class="btn-card border-0" onclick="toVendorData(`+ vendors[i].id +`)">
                    <div class="h6 p-2 mb-2">${items[i]}</div>
                     <div class="card mb-3" style="max-width: 540px;">
                        <div class="row no-gutters">
                          <div class="col-md-4">
                            <img src="${vendors[i].foto_lokasi}" class="card-img" alt="Image" style="width: 100%; height: 100%; object-fit: cover;">
                          </div>
                          <div class="col-md-8">
                            <div class="card-body">
                              <h6 class="card-title">${vendors[i].nama}</h6>
                              <p class="card-text text-muted">
                                ${vendors[i].jarak} km dari lokasi anda<br>
                              </p>
                              <div class="d-flex align-items-center mb-2">
                                <span class="mr-2">${vendors[i].rating}</span>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ml-2 text-muted">(${vendors[i].total_nota})</span>
                              </div>
                              <p class="card-text font-weight-bold">Rp. ${vendors[i].hargamin}-${vendors[i].hargamaks} <small>/${satuan} - ${layanan}</small></p>
                            <a href="https://www.openstreetmap.org/?mlat=${vendors[i].latitude}&mlon=${vendors[i].longitude}#map=15/${vendors[i].latitude}/${vendors[i].longitude}" target="_blank">Lihat di map</a>                            </div>
                          </div>
                        </div>
                      </div>
                      </button>
                      <br>`;
                }
                $('#untuk-anda').html(html);
                return "done";
            } catch (error) {
                console.error('Error:', error);
                return "error";

            }
        }

        async function pageLoad(idlayanan = 1) {
            $('#loading').show();
            $("#content").css("display","none");

            let latitude, longitude, statusUntukAnda, statusTerdekat;

            try {
                if (!isLoaded) {
                    statusDropDown = await pageLoadDropDownLayanan();
                }

                // Get the user's location
                const position = await ambilLokasiUser();
                latitude = position.coords.latitude;
                longitude = position.coords.longitude;
                // Load the necessary data

                if (!isLoaded) {
                    statusLayananTerdekat = await pageLoadLayananTerdekat(latitude, longitude);
                    statusTerdekat = await pageLoadTerdekat(latitude, longitude);

                }
                statusUntukAnda = await pageLoadUntukAnda(latitude, longitude, idlayanan);
                if (!isLoaded) {
                    if (statusDropDown == "done" && statusLayananTerdekat == "done" && statusUntukAnda == "done" &&
                        statusTerdekat == "done") {

                        $("#loading").hide();
                        $('#content').show();
                    } else {
                        console.log("Data loading failed. Statuses:", statusUntukAnda, statusTerdekat);
                        $('#loading').hide();
                    }

                }
                else{
                    if (statusUntukAnda == "done") {

                        $('#loading').hide();
                        $('#content').show();
                    } else {
                        console.log("Data loading failed. Statuses:", statusUntukAnda, statusTerdekat);
                        $('#loading').hide();
                    }
                }

            } catch (error) {
                console.error('Error getting location or loading data:', error);
                $('#loading').hide();
            }
        }

        function toVendorData(idvendor, detail = null, idlayanan = null){
            let url = `vendor/${idvendor}`;
            if(detail == 'layanan'){
                url += `/layanan/${idlayanan}`
            }
            window.location.href = url;
        }
        // Automatically get the location when the document is fully loaded
        $(document).ready(async function() {
            pageLoad();
            $('#layanans').change(function() {

                let selectedId = $(this).val();

                if (selectedId) {
                    pageLoad(selectedId);
                }
            });

        });
    </script>
@endsection
