@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
@endsection
@section('menu')
    <div class="text-center h2 pt-4">Keranjang</div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <label class="p-5 mb-0">Urutkan Berdasarkan:</label>
            <div class="select-container">
                <select class="form-control custom-select" style="width: 200px;">
                    <option>Most Recent</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
                <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
            </div>
            
        </div>
    </div>


    <div class="row">
        @php
            $totalVendors = count($vendors);
            $itemsPerRow = 4;
            $invisibleCards = ($itemsPerRow - ($totalVendors % $itemsPerRow)) % $itemsPerRow;
        @endphp
        <ul class="list-inline justify-content-center" style="display: flex; flex-wrap: wrap;">
            @foreach ($vendors as $v)
                <li class="list-inline-item p-3">
                    <a href="/cart/orders/{{$v->id}}" style="color: inherit; text-decoration: none;">
                        <div class="card" style="width: 18rem;">
                            <img src="{{ $v->foto_lokasi }}" class="card-img-top" style="height: 13rem;">
                            <div class="card-body">
                                <p class="card-text text-muted">{{ $v->nama }}</p>
                                <a href="#" class="text-primary">{{ $v->total_pemesanan }} Layanan</a>
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
    </div>
@endsection
