<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Singkat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

@php
    $splitcount = ceil(count($setup_layanans) / 2);
    $arr1 = array_slice($setup_layanans, 0, $splitcount);
    $arr2 = array_slice($setup_layanans, $splitcount);
@endphp

<body class="bg-light">
    <div class="container py-5">
        <div class="card mx-auto shadow-sm p-4" style="max-width: 500px;">
            <h3 class="text-center mb-3">Pengaturan singkat</h3>
            <p class="text-center text-muted mb-4">
                Pilih jasa yang ingin ditawarkan percetakan anda<br>
                <small>Pengaturan detil keperluan cetak akan disesuaikan dengan standar yang ada di sistem</small>
            </p>
            <form method="POST" action="/dosetup">
                @csrf
                <input type="hidden" name="idvendor" value="{{$idvendor}}">
                <div class="row">
                    <div class="col-6">
                        @foreach ($arr1 as $a1)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="{{ $a1->id }}"
                                    name="layanans[]" value="{{ $a1->id }}">
                                <label class="form-check-label" for="{{ $a1->id }}">{{ $a1->nama }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-6">
                        @foreach ($arr2 as $a2)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="{{ $a2->id }}"
                                    name="layanans[]" value="{{ $a2->id }}">
                                <label class="form-check-label" for="{{ $a2->id }}">{{ $a2->nama }}</label>
                            </div>
                        @endforeach
                    </div>
                    
                </div>


                <!-- Submit Button -->
                <input type="submit" value="Tambah Layanan" class="btn btn-primary w-100 mt-3">            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
