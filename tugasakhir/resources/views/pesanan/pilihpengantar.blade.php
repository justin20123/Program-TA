@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="#">Beranda</a></li>
  <li class="breadcrumb-item" aria-current="page">Pesanan</li>
  <li class="breadcrumb-item" aria-current="page">Detail Pesanan</li>
  <li class="breadcrumb-item active" aria-current="page">Pilih Pengantar</li>
</ol>

@endsection
@section('menu')
<h3 class="text-center p-4">Pesanan Vendor Anda</h1>
<h5 class="text-center">{{$nota_data->namaPemesan}}</h2>
<p class="text-center">Lokasi:</p>
<p class="text-center"><a href="https://www.openstreetmap.org/?mlat={{$nota_data->latitude_pengambilan}}&mlon={{$nota_data->longitude_pengambilan}}#map=15/{{$nota_data->latitude_pengambilan}}/{{$nota_data->longitude_pengambilan}}" target="_blank">Lihat di peta</a></p>
<br>
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
            <th>Pengantar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pengantar as $p)
    <tr class="justify-content-between">
        <td scope="row">
            {{$p->namapengantar}}
            
        </td>
        <td>
            <ul class="list-inline justify-content-between">

                
                <li class="list-inline-item">
                  <form action="/tugaskanpengantar" method="POST">
                    @csrf
                    <input type="hidden" name="idpengantar" value="{{$p->id}}">
                    <input type="hidden" name="idnota" value="{{$nota_data->id}}">
                    <input type="submit" value="Pilih Pengantar" class="btn btn-primary">
                  </form>
                </li>
            </ul>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endsection