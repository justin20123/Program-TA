
@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Vendors</li>
  </ol>
@endsection
@section('menu')
<h1 class="text-center p-5">Vendor Anda</h1>
@php
    $totalVendors = count($vendors);
    $itemsPerRow = 4; 
    $invisibleCards = ($itemsPerRow - ($totalVendors % $itemsPerRow)) % $itemsPerRow; // Calculate invisible cards needed
@endphp

<ul class="list-inline justify-content-center" style="display: flex; flex-wrap: wrap;">
@foreach ($vendors as $v)

<li class="list-inline-item p-2">
        <a href="{{ asset('/layanans/'. $v->id) }}">
            <div class="card" style="width: 18rem; height: 23rem">
                <img class="card-img-top" style="height: 12rem;" src="{{ $v->foto_lokasi }}" alt="Card image cap">
                <div class="card-body">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <div class="rating-images ">
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < round($v->vendor_rating))    
                                        <img class="img-fluid" src="{{ asset('../assets/images/rating.png') }}" alt="">
                                    @else
                                        <img style="opacity: 0.5;" class="img-fluid" src="{{ asset('../assets/images/rating.png') }}" alt="">
                                    @endif
                                @endfor
                            </div>
                        </li>
                        <li class="list-inline-item">( {{ $v->total_nota }} )</li>
                    </ul>
                    <h5 class="card-title">{{ $v->nama }}</h5>
                </div>
            </div>
        </a>
    </li>
@endforeach

@for ($i = 0; $i < $invisibleCards; $i++)
<li class="list-inline-item p-2">
    <div class="card" style="width: 18rem; height: 23rem; visibility: hidden;"> <!-- Use visibility: hidden to maintain layout -->
        <div class="card-body">
        </div>
    </div>
</li>
@endfor
</ul>
@endsection