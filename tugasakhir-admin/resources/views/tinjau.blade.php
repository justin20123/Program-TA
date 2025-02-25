@extends('layout.sneat')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Vendors</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendor 3</li>
    </ol>
@endsection

@section('menu')
    <div class="container">
        <h2 class="mt-4">{{ $vendor->nama }}</h2>
        <h4 class="mt-4">Komentar</h4>
        @if(count($ulasan) == 0)
        <p class="p">Belum ada ulasan pada periode ini</p>
        @endif
        @foreach ($ulasan as $u)
            <div class="card mb-3 border-primary">
                <div class="card-body d-flex">
                    <div>
                        <!-- User Name and Rating -->
                        <h5 class="card-title mb-1">
                            {{ $u['ulasan']->nama_pengguna }}
                            <small class="text-muted mx-3">{{ $u['ulasan']->waktu_selesai }}</small>
                        </h5>
                        <div class="text-warning">
                            @for ($i = 0; $i < 5; $i++)
                                @if ($i < round($u['ulasan']->rating))
                                    &#9733;
                                @else
                                    &#9734;
                                @endif
                            @endfor
                        </div>
                        <!-- Comment Text -->
                        <p class="card-text mt-2">
                            {{ $u['ulasan']->ulasan }}
                        </p>
                        <p class="card-text mt-2">
                            <strong>Pesanan:</strong>
                            <ul>
                                @foreach ($u['pesanan'] as $p)
                                    <li>{{ $p }}</li>
                                @endforeach
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    <script>
        // Additional scripts (if needed)
    </script>
@endsection
