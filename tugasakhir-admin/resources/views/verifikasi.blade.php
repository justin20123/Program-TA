@extends('layout.sneat')

@section('menu')
    <div class="modal fade" id="confirmTolakModal" tabindex="-1" role="dialog" aria-labelledby="confirmTolakModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmTolakModalLabel">Konfirmasi Penolakan Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modaltolaktext">
                    Apakah anda yakin ingin menolak vendor ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="tolakForm" action="{{ route('blokir') }}" method="POST">
                        @csrf
                        @method('put')
                        <div id="hiddens-tolak"></div>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmTerimaModal" tabindex="-1" role="dialog" aria-labelledby="confirmTerimaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmTerimaModalLabel">Konfirmasi Penerimaan Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modalterimatext">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="terimaForm" action="{{ route('aktifkan') }}" method="POST">
                        @csrf
                        @method('put')
                        <div id="hiddens-terima"></div>
                        <button type="submit" class="btn btn-primary">Setujui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <h4 class="mb-4">Vendor Menunggu Verifikasi</h4>


        @if (count($vendors) == 0)
            <div>
                <p>Tidak ada vendor yang perlu diaktifkan.</p>
            </div>
        @endif
        @foreach ($vendors as $v)
            <div class="card mb-3 px-4">
                <div class="row g-0 align-items-center">
                    <div class="col-md-2">
                        <img src="{{ $v->foto_lokasi }}" class="card-img-top" style="height: 11rem;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $v->nama }}</h5>
                            <p class="card-text">{{ $v->email }}</p>
                            <p class="card-text">
                                <small class="text-muted">Tanggal Daftar: {{ $v->tanggal_daftar }}</small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-danger btnopentolak" data-id="{{ $v->id }}" data-nama=" {{ $v->nama }}">Tolak</button>
                        <button class="btn btn-primary btnopenterima" data-id="{{ $v->id }}" data-nama=" {{ $v->nama }}">Terima</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Open rejection modal
            $(document).on('click', '.btnopentolak', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                $('#modaltolaktext').text("Apakah anda yakin ingin menolak vendor:" + nama +"?");
                $('#hiddens-tolak').html(`<input type="hidden" name="idvendor" value="${id}">`);
                $('#confirmTolakModal').modal('show');
            });

            // Open approval modal
            $(document).on('click', '.btnopenterima', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                $('#modalterimatext').text("Apakah anda yakin ingin menerima vendor:" + nama +"?");
                $('#hiddens-terima').html(`<input type="hidden" name="idvendor" value="${id}">`);
                $('#confirmTerimaModal').modal('show');
            });
        });
    </script>
@endsection
