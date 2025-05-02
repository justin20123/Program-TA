@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
    </ol>
@endsection
@section('menu')
    <div class="container-fluid">
        <br>
        <div class="row">
            <a href="../" class="px-4 pt-3 text-black">
                <i class="fas fa-arrow-left"></i>
            </a>    

            <div class="col-md-12">
                @if (Auth::user())
                    <h5>Selamat datang {{ Auth::user()->nama }}</h5>
                @endif

                <div class="d-flex justify-content-end align-items-center mb-3">
                    <label class="mr-2">Perbandingan Harga:</label>
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <div class="form-inline">
                        <div class="input-group">
                            <div class="select-container">
                                <select class="form-control custom-select px-4" name="layanans" id="layanans">
                                    @foreach($layanan_cetaks as $l)
                                        @if($l->id == $layanan_id)
                                        <option value="{{ $l->id }}" selected>{{ $l->nama }}</option>
                                        @else
                                        <option value="{{ $l->id }}">{{ $l->nama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    @if(count($vendors)==0)
                    <div class="py-3 mb-4">
                        <p class="h5">Belum ada vendor yang melayani layanan ini</p>
                    </div>
                    @endif
                    <div class="row g-4">
                        @foreach ($vendors as $v)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 pb-4">
                                <a href="/vendor/{{ $v->id }}" style="color: inherit; text-decoration: none;">
                                    <div class="card product-card border-0" style="height: 100%;">
                                        <img src="{{ asset('vendors/' . $v->id . '.' . $v->file_extension) }}"
                                            class="card-img-top" alt="Product Image"
                                            style="height: 200px; width: 100%; object-fit: contain;">
                                        <div class="card-body d-flex flex-column">
                                            <div class="rating">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
                                            <h6 class="card-title mt-1">{{ $v->nama }}</h6>
                                            <p class="text-primary mb-1">Rp.
                                                {{ number_format($v->hargamin, 0, '.', ',') }}-{{ number_format($v->hargamaks, 0, '.', ',') }}/{{ $layananvendor->satuan }}
                                            </p>
                                            <p class="text-muted small">{{ $layananvendor->nama }}</p>
                                            <p class="text text-success small">{{ $v->statusantar }}</p>
                                            <div class="mt-auto"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
$(document).ready(function() {
    $('#layanans').change(function() {
        let selectedId = $(this).val();

        if (selectedId) {
            // Redirect to the new URL
            window.location.href = `/vendor/layanan/${selectedId}`;
        }
    });
});
    </script>
@endsection
