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
    <div class="vendor-banner py-5" style="background-image: url('{{ $vendor->foto_lokasi }}'); background-color: rgba(255, 255, 255, 0.54);">
        
        <div class="overlay" style=""></div>
        <div class="content">
            {{ $vendor->nama }}
        </div>
    </div>

    <div class="search-bar">
        <form action="" method="GET" class="search-form">
            <div class="input-container">
                <input type="text" name="query" placeholder="Search..." class="search-input">
                <button type="submit" class="search-button">
                    <i class="fa fa-search" style="font-size: 16px;"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="layanans">
        <div class="row">
            @foreach($layanans as $l)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ $l->url_image }}" class="card-img-top" alt="{{ $l->nama }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $l->nama }}</h5>
                            <p class="card-text">Rp {{ $l->hargamin }} - {{ $l->hargamax }}</p>
                            <p class="card-text card-text-small">per {{ $l->satuan }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection