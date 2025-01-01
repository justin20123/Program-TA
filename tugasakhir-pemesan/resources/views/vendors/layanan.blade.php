@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
    </ol>
@endsection
@section('menu')
    {{-- banner --}}
    <div class="container-fluid p-0">
        <div class="vendor-banner py-5"
            style="background-image: url('{{ $vendor->foto_lokasi }}'); background-color: rgba(255, 255, 255, 0.54);">

            <div class="overlay" style=""></div>
            <div class="content">
                {{ $vendor->nama }}
            </div>
        </div>

        <div class="layanans">
            @php
                $totalLayanan = count($layanans);
                $itemsPerRow = 4;
                $invisibleCards = ($itemsPerRow - ($totalLayanan % $itemsPerRow)) % $itemsPerRow; // Calculate invisible cards needed
            @endphp

            @if ($totalLayanan > 0)
            <ul class="list-inline justify-content-center" style="display: flex; flex-wrap: wrap;">
                @foreach ($layanans as $l)
                    <li class="list-inline-item p-3">
                        <a href="{{ asset('vendor/' . $vendor->id . '/layanan/' . $l->id) }}" style="color: inherit; text-decoration: none;">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top" style="height: 13rem;" src="{{ $l->url_image }}" alt="{{asset('assets/images/noimg.jpg')}}">

                                <div class="card-body">

                                    <h5 class="card-title">{{ $l->nama }}</h5>
                                    <p class="card-text">Rp {{ $l->hargamin }} - {{ $l->hargamax }}</p>
                                    <p class="card-text card-text-small">per {{ $l->satuan }}</p>

                                    
                                </div>
                            </div>
                        </a>

                    </li>
                @endforeach

                @for ($i = 0; $i < $invisibleCards; $i++)
                    <li class="list-inline-item p-3">
                        <div class="card p-3" style="width: 18rem; visibility: hidden;">
                            
                            <div class="card-body">
                            </div>
                        </div>
                    </li>
                @endfor
            </ul>
            @else
            <p class="text-center">Belum ada layanan yang tersedia</p>
            @endif
        </div>
    </div>
@endsection
