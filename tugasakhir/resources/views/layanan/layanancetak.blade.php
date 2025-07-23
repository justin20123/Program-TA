@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Layanan</li>
    </ol>
@endsection
@section('menu')
    @if ($vendor->status == 'menunggu verifikasi')
        <div class="mx-5 mt-5">
            <p>Vendor Anda Belum Terverifikasi, Namun Anda Dapat Mengatur Vendor Anda Sekarang!</p>

        </div>
    @endif
    <section class="p-4">
      <div id="updatestatusinfo">

      </div>

        <h1 class="text-center p-5">{{ $vendor->nama }}</h1>
        @if ($vendor->status == 'active'||$vendor->status == "inactive")
        <div class="form-check pl-4">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="ubahaktif" id="ubahaktif" value="checkedValue"
                    checked>
                Menerima Pesanan
            </label>
        </div>
        @endif

        @php
            $totalLayanan = count($layanans);
            $itemsPerRow = 3;
            $invisibleCards = ($itemsPerRow - ($totalLayanan % $itemsPerRow)) % $itemsPerRow;
        @endphp

        <ul class="list-inline justify-content-center" style="display: flex; flex-wrap: wrap;">
            @foreach ($layanans as $l)
                <li class="list-inline-item p-3">
                    <a href="{{ asset('/layanans/' . $vendor->id . '/details/' . $l->id) }}">
                        <div class="card" style="width: 20rem;">
                            <img class="card-img-top" style="height: 13rem;" src="{{ $l->url_image }}"
                                alt="{{ asset('assets/images/noimg.jpg') }}">
                            <div class="card-body">

                                <h5 class="card-title">{{ $l->nama }}</h5>

                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        @for ($i = 0; $i < 5; $i++)
                                            @if ($i < round($l->layanan_rating))
                                                &#9733;
                                            @else
                                                &#9734;
                                            @endif
                                        @endfor
                                    </li>
                                    <li class="list-inline-item h6">({{ $l->total_nota }})</li>
                                    <li class="list-inline-item h6">Menerima Pesanan</li>
                                </ul>
                            </div>
                        </div>
                    </a>

                </li>
            @endforeach

            @for ($i = 0; $i < $invisibleCards; $i++)
                <li class="list-inline-item p-3">
                    <div class="card p-3" style="width: 20rem; visibility: hidden;">
                        <div class="card-body">
                        </div>
                    </div>
                </li>
            @endfor


        </ul>
        <div class="text-center">
            <a href="/setup/{{ $vendor->id }}" class="btn btn-primary m-2">Tambah Layanan</a><br>
            <a href="/editvendor/{{ $vendor->id }}" class="btn btn-primary m-2">Ubah Vendor</a>
            <input type="hidden" name="idvendor" id="idvendor" value="{{ $vendor->id }}">
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#ubahaktif').change(function() {
                var idvendor = $("#idvendor").val();
                var statusinfo = "Tidak aktif"
                var newStatus = "inactive";
                if (this.checked) {
                    newStatus = "active";
                    statusinfo = "Aktif"
                }
                $.ajax({
                    url: 'ubahaktif',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        idvendor: idvendor,
                        newStatus: newStatus
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(){
                      $("#updatestatusinfo").html('<div class="alert alert-primary">' +
                        'Status vendor berhasil diubah menjadi '+ statusinfo +
                      '</div>');
                    }
      
                });
            });
        });
    </script>
@endsection
