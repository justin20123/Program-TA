@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
    </ol>
@endsection
@section('menu')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h6>LAYANAN</h6>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="fotokopi" checked>
                <label class="form-check-label" for="fotokopi">Fotokopi</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="stiker">
                <label class="form-check-label" for="stiker">Stiker</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="banner">
                <label class="form-check-label" for="banner">Spanduk/Poster</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="calendar">
                <label class="form-check-label" for="calendar">Kalender</label>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <!-- Top Controls -->
            <div class="d-flex justify-content-between align-items-center my-3 header-controls">
                <input type="text" class="form-control" placeholder="Search for anything...">
                <div class="result-count">
                    65,067 Results Found
                </div>
            </div>

            <!-- Product Grid -->
            <div class="container">
                <div class="row g-4">
                    <!-- Row 1 -->
                    @foreach($vendors as $v)
                    <div class="col-12 col-md-3 pb-4">
                        <a href="/vendor/{{$v->id}}" style="color: inherit; text-decoration: none;">
                        <div class="card product-card border-0" style="height:29rem">
                            <img src="https://via.placeholder.com/150" class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <div class="rating">&#9733; &#9733; &#9733; &#9733; &#9733;</div> <!--bintang kosong = &#9733; -->
                                <h6 class="card-title mt-1">{{$v->nama}}</h6>
                                <p class="text-primary mb-1">Rp. {{number_format($v->hargamin, 0, '.', ',')}}-{{number_format($v->hargamaks, 0, '.', ',')}}/{{$layananvendor->satuan}}</p>
                                <p class="text-muted small">{{$layananvendor->nama}}</p>
                                <p class="text text-success small">{{ $v->statusantar }}</a>
                            </div>
                        </div>
                    </a>
                    </div>
                    @endforeach
                </div> <!-- End of Row -->
            </div>
        </div>
    </div>
</div>

@endsection