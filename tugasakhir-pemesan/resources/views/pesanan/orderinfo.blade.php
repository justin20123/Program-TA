@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">notas</li>
    </ol>
@endsection
@section('menu')
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
                    @if($prediksi_selesai && (count($arrSummaryReverse) < 4)) <!-- Sampai menunggu diambil --> 
                        <p class="mb-0 text-muted py-2">Perkiraan selesai proses: {{$prediksi_selesai}}</p>
                    @elseif(count($arrSummaryReverse) == 4) 
                        <p class="mb-0 text-muted py-2">Pesanan sudah selesai dibuat pada: {{$arrSummaryReverse[0]["waktu_progress_format"]}}, silahkan diambil</p>
                    @elseif(count($arrSummaryReverse) == 5) 
                        <p class="mb-0 text-muted py-2">Pesanan selesai pada: {{$arrSummaryReverse[0]["tanggal_selesai"]}}</p>
                    @endif
                @else
                    @if($prediksi_selesai && (count($arrSummaryReverse) < 5)) <!-- Sampai menunggu diambil --> 
                        <p class="mb-0 text-muted py-2">Perkiraan sampai: {{$prediksi_selesai}}</p>
                    @elseif(count($arrSummaryReverse) == 5) 
                        <p class="mb-0 text-muted py-2">Pesanan selesai pada: {{$arrSummaryReverse[0]["tanggal_selesai"]}}</p>
                    @endif
                @endif
            </div>


            
            <div class="progress-container">
                <div class="progress-bar"></div>
                <div class="progress-bar-filled"></div>
    
                <div class="progress-row">
                    <!-- Step 1 -->
                    <div class="step">
                        <div class="step-circle active">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <p class="step-label active">Pesanan Dibuat</p>
                    </div>
    
                    <!-- Step 2 -->
                    <div class="step">
                        <div class="step-circle active">
                            <i class="fas fa-box"></i>
                        </div>
                        <p class="step-label active">Diproses</p>
                    </div>
    
                    <!-- Step 3 -->
                    <div class="step">
                        
                        @if ($status_antar == 'diambil')
                        <div class="step-circle">
                            <i class="fas fa-gift"></i>
                        </div>
                        <p class="step-label">Menunggu diambil</p>
                        @else
                        <div class="step-circle">
                            <i class="fas fa-truck"></i>
                        </div>
                        <p class="step-label">Sedang Diantar</p>
                        @endif
                    </div>
    
                    <!-- Step 4 -->
                    <div class="step">
                        <div class="step-circle">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <p class="step-label">Selesai</p>
                    </div>
                </div>
            </div>

            <!-- Order Info -->
            <div class="mt-4">
                <h5 class="font-weight-bold">Order Activity</h5>
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

            @if(count($arrSummaryReverse) == 5)
            <button class="btn btn-primary">Review</button>
            @endif
        </div>
    </div>
</div>

@endsection