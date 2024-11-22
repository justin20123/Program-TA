@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">notas</li>
    </ol>
@endsection
@section('menu')
<div class="text-center h2 pt-4">Cart</div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <label class="p-5 mb-0">Sort by:</label>
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
            $totalNotas = count($notas);
            $itemsPerRow = 4;
            $invisibleCards = ($itemsPerRow - ($totalNotas % $itemsPerRow)) % $itemsPerRow;
        @endphp
        <ul class="list-inline justify-content-center" style="display: flex; flex-wrap: wrap;">
            @foreach ($notas as $n)
                <li class="list-inline-item p-3">
                    <a href="/pesanan/{{$n->idnota}}" style="color: inherit; text-decoration: none;">
                        <div class="card" style="width: 18rem;">
                            <img src="{{ $n->foto_lokasi }}" class="card-img-top" style="height: 13rem;">
                            <div class="card-body">
                                <p class="card-text text-dark">{{ $n->waktu_transaksi }}</p>
                                <p class="card-text text-dark">{{ $n->nama_vendor }}</p>
                                <p class="text-primary">{{ $n->jumlah_pesanan }} Layanan</p>
                                @if ($n->status == 'selesai')
                                     <p class="text-success">selesai</p>
                                @else
                                    <p class="text-dark">{{$n->status}}</p>
                                @endif
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