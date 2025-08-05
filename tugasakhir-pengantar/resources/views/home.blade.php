@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="#">Beranda</a></li>
    </ol>
@endsection
@section('menu')
<h2 class="h4 mx-4 my-2">Menunggu Diantar</h2>
<div class="accordion accordion-flush mx-2 mb-2" id="accordionFlushExample">
    @foreach ($layanan_menunggu_diantar as $md)
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed d-flex" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapse{{ $md['nota']->id }}" aria-expanded="false"
                    aria-controls="flush-collapseOne">
                    <span>{{ $md['nota']->nama_pengguna }}</span>
                    <span class="me-auto"></span>
                    <span class="me-auto"></span>
                    <span class="ms-auto">
                        {{-- <div>{{ $s['nota']->status }}</div>
                        <div>
                            @if ($s['nota']->opsi_pengambilan == 'diambil')
                                Tidak Diantar
                            @else
                                Diantar
                            @endif
                        </div> --}}
                    </span>
                </button>
            </h2>
            <div id="flush-collapse{{ $md['nota']->id }}" class="accordion-collapse collapse"
                aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <ul>
                        @foreach ($md['pesanan'] as $p)
                            
                            <li>{{ $p->jumlah }} lembar Fotokopi</li>
                        @endforeach
                    </ul>
                    <div class="d-flex justify-content-between py-3">
                        <a href="/detail/pengantaran/{{ $md['nota']->id }}"
                            class="btn btn-primary">Detail</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @if(!count($layanan_menunggu_diantar))
        <p class="h6 p-4 mt-3">Belum ada pesanan menunggu diantar</p>
    @endif
</div>

<h2 class="h4 mx-4 my-2">Selesai</h2>
<div class="accordion accordion-flush mx-2 mb-2" id="accordionFlushExample2">
    @foreach ($layanan_selesai as $s)
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button collapsed d-flex" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseTwo{{ $s['nota']->id }}" aria-expanded="false"
                    aria-controls="flush-collapseTwo">
                    <span>{{ $s['nota']->nama_pengguna }}</span>
                    <span class="me-auto"></span>
                    <span class="me-auto"></span>
                    <span class="ms-auto">
                        {{-- <div>{{ $s['nota']->status }}</div>
                        <div>
                            @if ($s['nota']->opsi_pengambilan == 'diambil')
                                Tidak Diantar
                            @else
                                Diantar
                            @endif
                        </div>
                    </span> --}}
                </button>
            </h2>
            <div id="flush-collapseTwo{{ $s['nota']->id }}" class="accordion-collapse collapse"
                aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample2">
                <div class="accordion-body">
                    <ul>
                        @foreach ($s['pesanan'] as $p)
                            <li>{{ $p->jumlah . ' ' . $p->satuan . ' ' . $p->layanan }}</li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>
    @endforeach
    @if(!count($layanan_selesai))
        <p class="h6 p-4 mt-3">Belum ada pesanan selesai</p>
    @endif
</div>

@endsection
    
