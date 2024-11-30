@extends('layout.sneat')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Verification</li>
    </ol>
@endsection

@section('menu')
    <!-- Confirmation Modal -->
    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/verifikasipesanan" method="POST">
                    @csrf
                    <div class="modal-body">
                        Apakah anda sudah setuju dengan contoh hasil cetakan ini?
                    </div>
                    <div id="hiddens">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancel">Cancel</button>
                        <input type="submit" class="btn btn-primary" value="Confirm">
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="container my-5">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


        <div class="card">
            <div class="card-body">
                <h5 class="font-weight-bold text-center">File Verification</h5>

                <div class="file-preview mb-4">
                    <div class="h5">File Pemesanan</div>
                    <iframe src="/{{ $pemesanan->url_file }}"
                        style="width: 100px; height: 100px; border: none; overflow: hidden;"></iframe>
                    <br>
                    <a class="btn btn-primary" href="/{{ $pemesanan->url_file }}" target="_blank">Preview File</a>

                </div>

                <!-- File Preview Section -->
                <div class="file-preview mb-4">
                    <div class="h5">File Untuk Verifikasi</div>
                    <iframe src="/{{ $notaProgress->url_ubah_file }}"
                        style="width: 100px; height: 100px; border: none; overflow: hidden;"></iframe>
                    <br>
                    <div class="text-right mt-2">
                        <a class="btn btn-primary" href="/{{ $notaProgress->url_ubah_file }}" target="_blank">Preview
                            File</a>
                        <button class="btn btn-primary" id="btnKonfirmasiHasil"
                            data-idnota=" {{ $notaProgress->notas_id }} "
                            data-idpemesanan=" {{ $notaProgress->pemesanans_id }} ">Konfirmasi Hasil</button>
                    </div>
                </div>

                <!-- Text Area for Changes -->
                <form id="formajukanperubahan" action="/ajukanperubahan" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="changeRequest">Ajukan Perubahan</label>
                        <textarea class="form-control" name="textperubahan" id="textperubahan" rows="3"
                            placeholder="Enter your changes here..."></textarea>
                        <input type="hidden" name="idnota" value="{{ $notaProgress->notas_id }}">
                        <input type="hidden" name="idpemesanan" value=" {{ $notaProgress->pemesanans_id }} ">
                        <input type="hidden" name="perubahan" id="perubahan" value=" {{ $notaProgress->pemesanans_id }} ">
                        <button type="button" class="btn btn-primary" id="btnSubmit">Submit</button>

                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $('#btnKonfirmasiHasil').on('click', function() {
                var idnota = $(this).data('idnota');
                var idpemesanan = $(this).data('idpemesanan');

                var html = `
                <input type="hidden" name="idnota" value="${idnota}">
                <input type="hidden" name="idpemesanan" value="${idpemesanan}">
                `

                $("#hiddens").html(html);

                $('#modalKonfirmasi').modal('show');
            });

            $('#submitChangeRequest').on('click', function() {
                const changes = $('#changeRequest').val();
                if (changes) {
                    // Send changes to the server
                    alert('Change request submitted: \n' + changes);
                    $('#changeRequest').val(''); // Clear the textarea
                } else {
                    alert('Please enter your changes before submitting.');
                }
            });

            $('#cancel').click(function() {
                $('#modalKonfirmasi').modal('hide');
            });

            $('#btnSubmit').click(function() {
                console.log('aa')
                var textperubahan = $("#textperubahan").val();
                $('#perubahan').val(textperubahan);
                $('#formajukanperubahan').submit();
            });
        });
    </script>
@endsection
