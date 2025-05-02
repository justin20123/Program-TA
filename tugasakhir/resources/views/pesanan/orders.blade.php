@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">List Pesanan</li>
    </ol>
@endsection
@section('menu')
    <div class="accordion accordion-flush" id="accordionFlushExample">
        @foreach ($nota_detail as $nd)
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                    <button class="accordion-button collapsed d-flex" type="button" data-bs-toggle="collapse"
                        data-bs-target="#flush-collapse{{ $nd['nota']->id }}" aria-expanded="false"
                        aria-controls="flush-collapseOne">
                        <span>{{ $nd['nota']->nama }}</span>
                        <span class="me-auto"></span>
                        <span class="me-auto"></span>
                        <span class="ms-auto">
                            <div>{{ $nd['nota']->status }}</div>
                            <div>
                                @if ($nd['nota']->opsi_pengambilan == 'diambil')
                                    Tidak Diantar
                                @else
                                    Diantar
                                @endif
                            </div>

                        </span>



                    </button>
                </h2>
                <div id="flush-collapse{{ $nd['nota']->id }}" class="accordion-collapse collapse"
                    aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <ul>
                            @foreach ($nd['pemesanans'] as $p)
                                <li>{{ $p->jumlah . ' ' . $p->satuan . ' ' . $p->layanan }}</li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between py-3">


                            @if (!$nd['nota']->waktu_menerima_pesanan)
                                <a href="/terimapesanan/{{ $nd['nota']->id }}" class="btn btn-primary">Terima Pesanan</a>
                            @else
                                <a href="/pesanancetak/{{ $nd['pemesanans'][0]->vendors_id }}/detail/{{ $nd['nota']->id }}"
                                    class="btn btn-primary">Detail Pesanan</a>
                            @endif

                        </div>

                    </div>
                </div>
        @endforeach
        @if(!count($nota_detail))
        <p class="h6 p-4 mt-3">Belum ada pesanan pada vendor anda</p>
        @endif

    </div>
@endsection
