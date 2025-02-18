@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item active" aria-current="page">Layanan</li>
  </ol>
@endsection
@section('menu')
<section class="p-4">
  <div class="input-group-prepend">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      Action
    </button>
    <div class="dropdown-menu" style="">
      <a class="dropdown-item" href="#">Action</a>
      <a class="dropdown-item" href="#">Another action</a>
      <a class="dropdown-item" href="#">Something else here</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" href="#">Separated link</a>
    </div>
  </div>
    <h1 class="text-center p-5">{{ $vendor->nama }}</h1>
<div class="form-check pl-4">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input" name="ubahaktif" id="" value="checkedValue" checked>
    Menerima Pesanan
  </label>
</div>

@php
  $totalLayanan = count($layanans);
  $itemsPerRow = 4; 
  $invisibleCards = ($itemsPerRow - ($totalLayanan % $itemsPerRow)) % $itemsPerRow; // Calculate invisible cards needed
@endphp

<ul class="list-inline justify-content-center" style="display: flex; flex-wrap: wrap;">
  @foreach ($layanans as $l)
  <li class="list-inline-item p-3">
    <a href="{{ asset('/layanans/'. $vendor->id . '/details/' . $l->id) }}">
        <div class="card" style="width: 20rem;">
        <img class="card-img-top" style="height: 13rem;" src="{{ $l->url_image }}" alt="{{asset('assets/images/noimg.jpg')}}">
            <div class="card-body">
                
            <h5 class="card-title">{{ $l->nama }}</h5>
        
            <ul class="list-inline">
              <li class="list-inline-item">
                @for ($i = 0; $i < 5; $i++)
                @if ($i < round($l->layanan_rating))    
                &#9733;
                @else
                &#9734;
                @endif
            @endfor
                  </li>
                  <li class="list-inline-item h6">({{ $l->total_nota }})</li>
                  <li class="list-inline-item h6">Menerima Pesanan</li>
              </ul>
            </div>
        </div>
    </a>
    
  </li>  
@endforeach

@for ($i = 0; $i < $invisibleCards; $i++)
    <li class="list-inline-item p-3">
        <div class="card p-3" style="width: 20rem; visibility: hidden;"> <!-- Use visibility: hidden to maintain layout -->
            <div class="card-body">
            </div>
        </div>
    </li>
@endfor


</ul>
<div class="text-center">
  <a href="/setup/{{$vendor->id}}" class="btn btn-primary m-2">Tambah Layanan</a><br>
  <a href="/editvendor/{{$vendor->id}}" class="btn btn-primary m-2">Edit Vendor</a>
</div>    
</section>

@endsection