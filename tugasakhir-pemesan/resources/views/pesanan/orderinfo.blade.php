@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Proses Pemesanan</li>
    </ol>
@endsection
@section('menu')

<!-- modal -->
<div class="modal fade" id="modalreview" tabindex="-1" aria-labelledby="modalreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalreviewLabel">Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Star Ratings Section -->
                <form method="POST" action="{{ route('doreview') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Kualitas Hasil dan Kesesuaian Dengan Keinginan</label><br />
                        <div class="star-rating" data-input-id="ratingkualitas">
                            <span class="star" data-value="1">&#9734;</span>
                            <span class="star" data-value="2">&#9734;</span>
                            <span class="star" data-value="3">&#9734;</span>
                            <span class="star" data-value="4">&#9734;</span>
                            <span class="star" data-value="5">&#9734;</span>
                        </div>
                        <input type="hidden" name="ratingkualitas" id="ratingkualitas" value="0" />
                    </div>

                    <div class="mb-3">
                        <label>Pelayanan Pelanggan</label><br />
                        <div class="star-rating" data-input-id="ratingpelayanan">
                            <span class="star" data-value="1">&#9734;</span>
                            <span class="star" data-value="2">&#9734;</span>
                            <span class="star" data-value="3">&#9734;</span>
                            <span class="star" data-value="4">&#9734;</span>
                            <span class="star" data-value="5">&#9734;</span>
                        </div>
                        <input type="hidden" name="ratingpelayanan" id="ratingpelayanan" value="0" />
                    </div>

                    <div class="mb-3">
                        <label>Fasilitas yang Disediakan</label><br />
                        <div class="star-rating" data-input-id="ratingfasilitas">
                            <span class="star" data-value="1">&#9734;</span>
                            <span class="star" data-value="2">&#9734;</span>
                            <span class="star" data-value="3">&#9734;</span>
                            <span class="star" data-value="4">&#9734;</span>
                            <span class="star" data-value="5">&#9734;</span>
                        </div>
                        <input type="hidden" name="ratingfasilitas" id="ratingfasilitas" value="0" />
                    </div>

                    @if($status_antar =="diantar")
                    <div class="mb-3">
                        <label>Pengantaran</label><br />
                        <div class="star-rating" data-input-id="ratingpengantaran">
                            <span class="star" data-value="1">&#9734;</span>
                            <span class="star" data-value="2">&#9734;</span>
                            <span class="star" data-value="3">&#9734;</span>
                            <span class="star" data-value="4">&#9734;</span>
                            <span class="star" data-value="5">&#9734;</span>
                        </div>
                        <input type="hidden" name="ratingpengantaran" id="ratingpengantaran" value="0" />
                    </div>
                    @endif

                    <input type="hidden" name="idnota" value="{{$nota->id}}" />
                    <input type="hidden" name="statusantar" value="{{$status_antar}}" />

                    <div class="mb-3">
                        <label>Komentar</label>
                        <textarea class="form-control" name="komentar" rows="4" placeholder="Write here.."></textarea>
                    </div>

                    <!-- Save Button -->
                    <div class="text-end">
                        <input type="submit" class="btn btn-primary" value="Save Details">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <!-- Header Section -->
            <div class="p-3" style="background-color: #fffbe6; border-radius: 5px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">{{$jumlah_pesanan}} Barang • Pesanan dilakukan pada {{$waktustart}}</span>
                    </div>
                    <div>
                        <span class="font-weight-bold text-primary" style="font-size: 1.5rem;">Rp. {{ number_format($harga_total, 0, '.', ',') }}</span>
                    </div>
                </div>
            </div>
            <div>
                @if ($status_antar == 'diambil')
                    @if($prediksi_selesai && ($totalproses < 4)) <!-- Sampai menunggu diambil --> 
                        <p class="mb-0 text-muted py-2">Perkiraan selesai proses: {{$prediksi_selesai}}</p>
                    @elseif($totalproses == 4) 
                        <p class="mb-0 text-muted py-2">Pesanan sudah selesai dibuat pada: {{$arrSummaryReverse[0]["waktu_progress_format"]}}, silahkan diambil</p>
                    @elseif($totalproses == 5) 
                        <p class="mb-0 text-muted py-2">Pesanan selesai pada: {{$arrSummaryReverse[0]["tanggal_selesai"]}}</p>
                    @endif
                @else
                    @if($prediksi_selesai && ($totalproses < 5)) <!-- Sampai menunggu diambil --> 
                        <p class="mb-0 text-muted py-2">Perkiraan sampai: {{$prediksi_selesai}}</p>
                    @elseif($totalproses == 5) 
                        <p class="mb-0 text-muted py-2">Pesanan selesai pada: {{$arrSummaryReverse[0]["tanggal_selesai"]}}</p>
                    @endif
                @endif
            </div>


            
            <div class="stepper">
                <div class="step-item active">
                    <div class="step">
                        <span class="step-number">1</span>
                        <span class="step-title">Pesanan Dibuat</span>
                    </div>
                </div>
                <div class="step-item active">
                    <div class="step">
                        <span class="step-number">2</span>
                        <span class="step-title">Diproses</span>
                    </div>
                </div>
                @if (!$nota->waktu_tunggu_diambil && !$nota->waktu_diantar)
                    <div class="step-item">
                    @else
                        <div class="step-item active">
                @endif
                <div class="step">
                    <span class="step-number">3</span>
                    @if ($status_antar == 'diantar')
                        <span class="step-title">Sedang Diantar</span>
                    @elseif ($status_antar == 'diambil')
                        <span class="step-title">Menunggu Diambil</span>
                    @endif
                </div>
            </div>
            @if (!$nota->waktu_selesai)
                <div class="step-item">
                @else
                    <div class="step-item active">
            @endif
            <div class="step">
                <span class="step-number">4</span>
                <span class="step-title">Selesai</span>
            </div>
            </div>
            </div>
            <div class="container-xxl pb-container" style="width: 85%">
                <div class="progress" style="width: 100%">
                    @if (!$nota->waktu_tunggu_diambil && !$nota->waktu_diantar)
                        <div class="progress-bar" style="width: 35%"></div>
                    @elseif (!$nota->waktu_selesai)
                        <div class="progress-bar" style="width: 65.75%"></div>
                    @else
                        <div class="progress-bar" style="width: 100%"></div>
                    @endif
                </div>
            </div>

            <!-- Order Info -->
            <div class="mt-4">
                <h5 class="font-weight-bold">Proses Pemesanan</h5>
                <ul class="list-group list-group-flush">
                    @foreach ($arrProgressReverse as $key=>$ap)
                        <li class="list-group-item">
                            <div> {{$ap['progress']}} </div>
                            @if($ap['progress'] == 'Menunggu verifikasi')
                            <a class="text text-primary" href="/verifikasi/{{ $ap['pemesanans_id'] }}/{{ $ap['notas_id'] }}/{{ $ap['urutan_progress'] }}">Verifikasi</a>
                            <br>
                            @endif
                            <small class="text-muted">{{$ap['waktu_progress_format']}}</small>
                        </li>
                    @endforeach
                    
                    @foreach ($arrSummaryReverse as $key=>$as)
                        <li class="list-group-item">
                            <div> {{$as['progress']}} </div>
                            <small class="text-muted">{{$as['waktu_progress_format']}}</small>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
            @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
            @if($totalproses == 5)
                @if(!$israted)
                <div style="display: flex; justify-content: center;">
                    <button class="btn btn-primary" id="btnopenreview">Review</button>
                </div>
                @else
                <p class="text text-success">Anda sudah melakukan rating pada pesanan ini</p>
                @endif
            @endif
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {

    $('#btnopenreview').click(function () { 
        $('#modalreview').modal('show');
        
    });
    $('.star-rating .star').on('click', function() {
        const ratingGroup = $(this).closest('.star-rating');
        const value = parseInt($(this).data('value'));
        const inputSelector = ratingGroup.data('input-id');
        $(`#${inputSelector}`).val(value);

        console.log(inputSelector + ' value=' + $(`#${inputSelector}`).val());

        ratingGroup.find('.star').each(function() {
            const sValue = parseInt($(this).data('value'));
            if (sValue <= value) {
                $(this).addClass('selected').html('&#9733;'); // Change to selected star
            } else {
                $(this).removeClass('selected').html('&#9734;'); // Change to unselected star
            }
        });
    });

});
</script>
@endsection