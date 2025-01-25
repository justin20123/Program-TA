@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cart</li>
    </ol>
@endsection
@section('menu')
    <div class="modal fade" id="confirmLepasBlokirModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmLepasBlokirModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmLepasBlokirModalLabel">Persetujuan Lepas Blokir Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin melepas blokir vendor ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batalkan</button>
                    <form action="{{ route('aktifkan') }}" method="POST">
                        @csrf
                        @method('put')
                        <div id="hiddens">

                        </div>
                        <button type="submit" class="btn btn-danger">Lepas Blokir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h4 class="mb-4">Vendor Terblokir</h4>
        @if (count($vendors) == 0)
            <div>
                <p>Tidak ada vendor yang sedang diblokir.</p>
            </div>
        @endif
        @foreach ($vendors as $key => $v)
            <div class="card mb-3 px-4">
                <div class="row g-0 align-items-center">
                    <div class="col-md-2">
                        <img src="{{ $v->foto_lokasi }}" class="card-img-top" style="height: 11rem;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $v->nama }}</h5>
                            <h5 class="card-title">{{ $v->email }}</h5>


                            <p class="card-text">
                                <small>
                                    Tanggal diblokir: {{ $v->tanggal_diblokir }}
                                </small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-danger" id="btnopenlepasblokir" data-id="{{ $v->id }}">Lepas
                            Blokir</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#btnopenlepasblokir').click(function() {
                const id = $(this).data('id');
                $('#hiddens').html(
                    `<input type="hidden" name="idvendor" value="${id}">`
                );
                $('#confirmLepasBlokirModal').modal('show');
            });

        });
    </script>
@endsection
