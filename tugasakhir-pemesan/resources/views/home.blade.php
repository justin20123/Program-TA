
@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Vendors</li>
  </ol>
@endsection
@section('menu')
<div class="d-flex justify-content-between align-items-center px-4 pt-3">
    <div class="h5 p-2">List Vendor</div>
    <a href="#" class="text-danger h5 p-2">Lihat Semua</a></div>
<div class="container mt-5">
    <div class="row" id="vendorsJarak">
    </div>
</div>
    <p id="status"></p>
@endsection

@section('script')
<script>
    // Function to get the user's location
    function sendLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Send the latitude and longitude to the Laravel backend using jQuery
                $.ajax({
                    url: '  location',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    success: function(data) {
                        html = "";
                        $.each(data['data'], function(index, item) {
                            console.log(item);
                            html += `
                            <div class="col-md-3">
                                <div class="card">
                                    <img style='width:15rem; height:12rem;' src="`+ item['foto_lokasi'] +`" class="card-img-top" alt="Card image 1">
                                    <div class="card-body">
                                        <h6 class="card-title">`+ item['nama'] +`</h6>
                                        <p class="card-text">` + item['jarak'] +` km dari lokasi anda</p>
                                        <a href="https://www.openstreetmap.org/?mlat=` + item['latitude'] +`&mlon=` + item['longitude'] +`#map=15/` + item['latitude'] +`/` + item['longitude'] +`" target="_blank">Lihat di map</a>
                                    </div>
                                </div>
                            </div>
                            `;
                        })
                        $('#vendorsJarak').html(html);
                        // $('#status').text('Location sent successfully: ' + JSON.stringify(data));
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:'+ error);
                        console.error(xhr.responseText);
                        $('#status').text('Error sending location. '+ xhr);
                    }
                });
            }, function(error) {
                handleError(error);
            });
        } else {
            $('#status').text("Geolocation is not supported by this browser.");
        }
    }

    // Handle errors from geolocation
    function handleError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                $('#status').text("User  denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                $('#status').text("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                $('#status').text("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                $('#status').text("An unknown error occurred.");
                break;
        }
    }

    // Automatically get the location when the document is fully loaded
    $(document).ready(function() {
        sendLocation();
    });
</script>
@endsection