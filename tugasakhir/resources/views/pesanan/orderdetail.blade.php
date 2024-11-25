@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item" aria-current="page">Vendors</li>
    <li class="breadcrumb-item" aria-current="page">Orders</li>
    <li class="breadcrumb-item active" aria-current="page">Order Detail</li>
</ol>

@endsection
@section('menu')

<div id="modalKirimContoh" class="modal">
  <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Submit Data</h2>
      <form action="kirimcontoh">
          <input type="file" name="fileperubahan" id="fileperubahan">
          <input type="hidden" name="idpemesanan" id="idpemesananhidden">
          <button type="submit">Submit</button>
      </form>
  </div>
</div>

<div id="error-message" style="color: red; display: none;"></div>
<h3 class="text-center p-4">Pesanan Vendor Anda</h1>
<h5 class="text-center">{{$notaDetail[0]['nota']->nama}}</h2>
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
    <div class="step-item">
      <div class="step">
        <span class="step-number">3</span>
        <span class="step-title">Sedang Diantar</span>
      </div>
    </div>
    <div class="step-item">
      <div class="step">
        <span class="step-number">4</span>
        <span class="step-title">Selesai</span>
      </div>
    </div>
  </div>
  <div class="container-xxl pb-container" style="width: 85%">
    <div class="progress" style="width: 100%">
        <div class="progress-bar" style="width: 35%"></div>
    </div>
</div>
  
<table class="table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Item</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($notaDetail[0]['pemesanans'] as $p)
    <tr class="justify-content-between">
        <td>
            <img class="od-image" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1925379617b%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1925379617b%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22107.1937484741211%22%20y%3D%2296.24000034332275%22%3E286x180%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="" srcset="">        
        </td>
        <td scope="row">
            <div>{{$p->layanan}}</div>
            <div>{{$p->jumlah . ' '. $p->satuan}}</div>
            <div>Rp. {{ number_format($p->harga_satuan * $p->jumlah, 0, ',', '.') }}</div>
            <a href="{{ route('file', ['url_file' => $p->url_file]) }}" class="btn btn-primary py-2" target="_blank">Download File</a>
          </td>
        <td>
            <ul class="list-inline justify-content-between">

                <li class="list-inline-item">
                    <a href="pesanan/perubahan/{{$p->id}}" class="btn btn-primary">Lihat Perubahan</a>
                </li>
                <li class="list-inline-item">
                    <button class="btn btn-primary kirimcontoh" data-idpemesanan="{{$p->id}}">Kirimkan Contoh</button>
                </li>
                <li class="list-inline-item">
                    <a href="editdetail/edit/option" class="btn btn-primary">Unmark Done</a>
                </li>
            </ul>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
<a class="btn btn-primary text-center my-5" href="../../../pesanan/{{$notaDetail[0]['pemesanans'][0]->vendors_id}}/pengantar/{{$notaDetail[0]['nota']->id}}">Antar</a>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.download-btn').click(function() {
        var filename = $(this).data('url'); 
        var fileUrl = '/file/' + filename; // Construct the URL
        alert('File URL: ' + fileUrl);
        console.log('Fetching URL:', fileUrl); // Log the URL

        // Use fetch to download the file
        fetch(fileUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.blob(); // Convert the response to a Blob
            })
            .then(blob => {
                if (blob.size === 0) {
                    throw new Error('The file is empty.'); // Handle empty file case
                }
                
                // Create a temporary URL for the Blob
                var url = window.URL.createObjectURL(blob);
                
                // Create a link element
                var a = document.createElement('a');
                a.href = url;
                a.download = filename; // Use the filename for the download
                document.body.appendChild(a);
                a.click(); // Trigger the click
                a.remove(); // Clean up the link
                window.URL.revokeObjectURL(url); // Release the Blob URL
            })
            .catch(error => {
                // Display the error message to the user
                $('#error-message').text('There was a problem with the fetch operation: ' + error.message).show();
                console.error('Fetch error:', error);
            });
    });
    
    $(document).on('click', '.kirimcontoh', function() {
      var button = $(this);
      var idpemesanan = button.data('idpemesanan');
      
      $('#idpemesananhidden').val(idpemesanan);
      $('#modalKirimContoh').modal('show');
    });
});
  </script>
@endsection