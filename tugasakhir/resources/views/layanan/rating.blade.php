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
        <h2 class="mt-4">{{ $layanan->nama }}</h2>
        <h4 class="mt-4">Ulasan</h4>

        @foreach ($pemesanan as $p)
            <div class="card mb-3 border-primary">
                <div class="card-body d-flex">
                    <!-- User Profile Picture -->
                    <div class="me-3">
                        <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Profile">
                    </div>
                    <div>
                        <!-- User Name and Rating -->
                        <h5 class="card-title mb-1">{{ $p->nama_pemesan }}</h5>
                        <div class="text-warning">
                            @for ($i = 0; $i < 5; $i++)
                                @if ($i < round($p->rating))
                                    &#9733;
                                @else
                                    &#9734;
                                @endif
                            @endfor
                        </div>
                        <!-- Comment Text -->
                        <p class="card-text mt-2">
                            {{ $p->ulasan }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach

        @if(count($pemesanan) == 0)
        <p class="text-center mt-5">Layanan ini belum terdapat ulasan</p>
        @endif
    </div>
@endsection

@section('script')
    <script>
        // Additional scripts (if needed)
    </script>
@endsection
