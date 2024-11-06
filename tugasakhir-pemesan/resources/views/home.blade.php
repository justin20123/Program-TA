
@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Vendors</li>
  </ol>
@endsection
@section('menu')
<div id="untuk-anda" class='p-5'>
    
</div>
<div class="d-flex justify-content-between align-items-center px-4 pt-3">
    <div class="h5 p-2">List Vendor</div>
    <a href="#" class="text-danger h5 p-2">Lihat Semua</a></div>
<div class="container mt-5">
    <div class="row" id="vendorsJarak">
    </div>
</div>
    <p id="status" class="px-5">Loading Data...</p>

@endsection

@section('script')
<script>
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
    //kirim lokasi ke controller
    function kirimLokasi(latitude, longitude) {
        return $.ajax({
            url: 'location',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ latitude, longitude }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
            }
        });
    }
    function ambilDataVendorTerdekat(data) {
        let html = "";
        $.each(data['data'], function(index, item) {
            html += `
            <div class="col-md-3">
                <div class="card">
                    <img style='width:15rem; height:12rem;' src="` + item['foto_lokasi'] + `" class="card-img-top" alt="Card image 1">
                    <div class="card-body">
                        <h6 class="card-title">` + item['nama'] + `</h6>
                        <p class="card-text">` + item['jarak'] + ` km dari lokasi anda</p>
                        <a href="https://www.openstreetmap.org/?mlat=` + item['latitude'] + `&mlon=` + item['longitude'] + `#map=15/` + item['latitude'] + `/` + item['longitude'] + `" target="_blank">Lihat di map</a>
                    </div>
                </div>
            </div>
            `;
        });
        $('#vendorsJarak').html(html);
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

    async function pageLoadTerdekat() {
        $('#status').text("Loading Data...");

        try {
            const position = await ambilLokasiUser();
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Send the location to the backend
            const locationResponse = await kirimLokasi(latitude, longitude);
            ambilDataVendorTerdekat(locationResponse);

            $('#status').text(''); // Clear loading message
        } catch (error) {
            $('#status').text(error);
            console.error('Error:', error);
        }
    }

    async function pageLoadUntukAnda(latitude, longitude, idlayanan) {
        try {
            const response = await kirimReqUntukAnda(latitude, longitude, idlayanan);
            console.log(response);
            data = response['data']
            let layanan = data['layanan'];
            let satuan = data['satuan'];
            let vendors = data['vendors'];
            let html = `<div class="h5 p-4 pb-2">Untuk Anda</div>
                     <hr>`
            let items = ['Terdekat','Termurah','Direkomendasikan']
            for (let i = 0; i < items.length; i++) {
                console.table(vendors);
                html += `<div class="h6 p-2 mb-2">${items[i]}</div>
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
                              <p class="card-text font-weight-bold">Rp. ${vendors[i].hargamin}-${vendors[i].hargamaks} <small>/lembar - Fotokopi</small></p>
                            <a href="https://www.openstreetmap.org/?mlat=${vendors[i].latitude}&mlon=${vendors[i].longitude}#map=15/${vendors[i].latitude}/${vendors[i].longitude}" target="_blank">Lihat di map</a>                            </div>
                          </div>
                        </div>
                      </div>`;
            }
            $('#untuk-anda').html(html);
            
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Automatically get the location when the document is fully loaded
    $(document).ready(function() {
        pageLoadTerdekat();
        ambilLokasiUser().then(position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            pageLoadUntukAnda(latitude, longitude, 1); // Load Topsis data
        }).catch(error => {
            console.error('Error getting location:', error);
        });
    });
</script>
@endsection