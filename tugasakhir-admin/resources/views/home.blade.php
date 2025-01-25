@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
@endsection
@section('menu')
<div class="modal fade" id="confirmBlokirModal" tabindex="-1" role="dialog" aria-labelledby="confirmBlokirModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmBlokirModalLabel">Konfirmasi Penolakan Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin memblokir vendor ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form action="{{ route('blokir') }}" method="POST">
                        @csrf
                        @method('put')
                        <div id="hiddens"></div>
                        <button type="submit" class="btn btn-danger">Blokir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <h4 class="mb-4">Ulasan Mingguan</h4>
        @if(count($vendors) <1)
            <div>
                <p>Belum ada vendor yang aktif</p>
            </div>
        @endif
        @foreach ($vendors as $key => $v)
            <div class="card mb-3 px-4 ">
                <div class="row g-0 align-items-center">
                    <div class="col-md-2">
                        <img src="{{ $v->foto_lokasi }}" class="card-img-top" style="height: 11rem;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $v->nama }}</h5>
                            @if ($v->rating > 0 && $v->rating < 3)
                            <p class="card-text">Ulasan minggu ini:
                                <span class="text-danger">
                                    {{ $v->rating }}
                                </span>
                            </p>
                            @else
                                <p class="card-text">Ulasan minggu ini: {{ $v->rating }}</p>
                            @endif
                            

                            <p class="card-text">
                                <small>
                                    {{ $rating_rendah }} Ulasan rendah minggu ini
                                </small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        @if ($v->rating > 0 && $v->rating < 3)
                            <a href="{{ route('tinjau') }}" class="btn btn-primary">Tinjau</a>
                        @endif
                        <button class="btn btn-danger" id="btnopenblokir" data-id="{{ $v->id }}">Blokir Vendor</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#btnopenblokir').click(function() {
                const id = $(this).data('id');
                $('#hiddens').html(
                    `<input type="hidden" name="idvendor" value="${id}">`
                );
                $('#confirmBlokirModal').modal('show');
            });

        });
    </script>
@endsection
