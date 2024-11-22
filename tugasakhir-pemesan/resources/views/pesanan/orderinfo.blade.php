@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">notas</li>
    </ol>
@endsection
@section('menu')
<div class="container my-4">
    <div class="card">
        <div class="card-body">
            <!-- Header Section -->
            <div class="d-flex justify-content-between">
                <div>
                    <p class="mb-1">2 Layanan • Pesanan dilakukan pada 17 April, 2024 at 16:00</p>
                    <h4 class="font-weight-bold text-primary">Rp. 355.000</h4>
                </div>
                <div>
                    <p class="mb-0 text-muted">Perkiraan sampai: 24 April 2024</p>
                </div>
            </div>
            <hr>

            <!-- Progress Bar Section -->
            <div class="progress mb-4">
                <div class="progress-bar progress-bar-custom" style="width: 75%;" role="progressbar"></div>
            </div>
            <div class="d-flex justify-content-between">
                <div class="step">
                    <img src="https://via.placeholder.com/32" alt="step1">
                    <span>Pesanan Dibuat</span>
                </div>
                <div class="step">
                    <img src="https://via.placeholder.com/32" alt="step2">
                    <span>Diproses</span>
                </div>
                <div class="step">
                    <img src="https://via.placeholder.com/32" alt="step3">
                    <span>Sedang Diantar</span>
                </div>
                <div class="step">
                    <img src="https://via.placeholder.com/32" alt="step4">
                    <span>Selesai</span>
                </div>
            </div>

            <!-- Order Activity Section -->
            <div class="order-activity mt-4">
                <h5 class="font-weight-bold">Order Activity</h5>
                <ul class="list-unstyled">
                    <li>
                        <p class="mb-0">Pesanan sedang diantar</p>
                        <small class="text-muted">22 April, 2024 14:00</small>
                    </li>
                    <li>
                        <p class="mb-0">Pesanan selesai diproses</p>
                        <small class="text-muted">21 April, 2024 19:32</small>
                    </li>
                    <li>
                        <p class="mb-0">Menunggu Verifikasi Hasil</p>
                        <small class="text-muted">21 April, 2024 19:32</small>
                    </li>
                    <li>
                        <p class="mb-0">Pesanan sedang diproses</p>
                        <small class="text-muted">20 April, 2024 19:32</small>
                    </li>
                    <li>
                        <p class="mb-0">Pesanan berhasil diverifikasi</p>
                        <small class="text-muted">20 April, 2024 17:30</small>
                    </li>
                    <li>
                        <p class="mb-0">Pesanan berhasil dilakukan</p>
                        <small class="text-muted">20 April, 2024 17:00</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection