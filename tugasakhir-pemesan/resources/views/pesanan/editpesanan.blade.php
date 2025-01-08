@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
    </ol>
@endsection
@section('menu')
    <div class="container py-5">
        <div id="error-message"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-4">
                    <img src="https://picsum.photos/300/200" alt="Main Product" class="img-fluid rounded">
                </div>
                <div class="d-flex mb-4">
                    <img src="https://picsum.photos/300/200" class="img-thumbnail px-2" style="width: 75px; height: 75px;"
                        alt="Thumbnail 1">
                    <img src="https://picsum.photos/300/200" class="img-thumbnail px-2" style="width: 75px; height: 75px;"
                        alt="Thumbnail 2">
                    <img src="https://picsum.photos/300/200" class="img-thumbnail px-2" style="width: 75px; height: 75px;"
                        alt="Thumbnail 3">
                </div>


                <h2 class="h4 font-weight-bold">Harga (1 {{ $layanan->satuan }} = {{ $layanan->kesetaraan_pcs }} pcs)</h2>
                <ul class="list-unstyled text-muted mb-0" id='listharga'>
                    @foreach ($hargacetaks as $h)
                        @if ($h->jumlah_cetak_maksimum == null)
                            <li>&gt;{{ $h->jumlah_cetak_minimum - 1 }} {{ $layanan->satuan }} = Rp.
                                {{ $h->harga_satuan }}/{{ $layanan->satuan }}</li>
                        @else
                            <li>{{ $h->jumlah_cetak_minimum }}–{{ $h->jumlah_cetak_maksimum }} {{ $layanan->satuan }} = Rp.
                                {{ $h->harga_satuan }}/{{ $layanan->satuan }}</li>
                        @endif
                    @endforeach
                </ul>
                <input type="hidden" id="idvendor" value="{{ $jenisbahan[0]->idvendor }}">
                <input type="hidden" id="idlayanan" value="{{ $layanan->id }}">
                <input type="hidden" id="satuan" value="{{ $layanan->satuan }}">

            </div>

            <div class="col-md-8">
                <h1 class="display-4">{{ $layanan->nama }}</h1>

                <form action="" method="post" id='form'>
                    @csrf
                    <div class="form-group">
                        <label for="paperType" class="font-weight-bold">Pilih Jenis dan Bahan</label>
                        <div class="select-container">
                            <select class="form-control custom-select px-4" name="jenisbahan" id="jenisbahan">
                                @foreach ($jenisbahan as $key => $jb)
                                    @if ($key == $idjenisbahan)
                                        <option value="{{ $jb->id }}" selected>{{ $jb->nama }}</option>
                                    @else
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                        </div>
                    </div>
                    <br>
                    <div id="listdetail">
                        @if (count($opsidetail) > 0)
                            @foreach ($opsidetail as $key => $od)
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ $od['detail']->value }}</label>
                                    <div class="select-container">
                                        <select class="form-control custom-select px-4"
                                            name="opsidetail-{{ $key }}" id="opsidetail-{{ $key }}">
                                            @foreach ($od['opsi'] as $o)
                                                <option value="{{ $o['id'] }}">{{ $o['opsi'] }} (+Rp.
                                                    {{ $o['biaya_tambahan'] }})</option>
                                            @endforeach
                                        </select>
                                        <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                                    </div>
                                </div>
                                <br>
                            @endforeach
                        @else
                            <p>Tidak ada opsi yang perlu ditambahkan</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label id="labelQuantity" class="font-weight-bold mr-3">Jumlah</label>

                        <input type="number" id="jumlahcopy" class="form-control w-25" min="1" value="1"
                            required disabled>
                    </div>
                    <br>

                    <div class="form-group d-flex">

                        <a href="/cart" class="btn btn-outline-secondary">Keranjang</a>
                    </div>
                    <br>
                    <hr>

                    <div class="form-group mt-4">
                        <label for="catatan" class="font-weight-bold">Catatan</label>
                        <textarea id="catatan" class="form-control" rows="3" placeholder="Catatan"></textarea>
                    </div>
                    <br>

                    <div class="form-group" id="fg-input-file">
                        <label for="fileUpload" class="font-weight-bold">Unggah Dokumen</label>
                        <div id="upload">
                            
                        </div>
                        <div id="file-detail">
                            <div class="file-name-container d-flex justify-content-between">
                                <div id="file-name"></div>
                                <div id="hapus-file" class="btn btn-danger">X</div>
                            </div>


                            <button type="button" id="lihat-file" class="btn btn-primary">Lihat PDF</button>
                        </div>
                    </div>

                    <input type="hidden" name="idpemesanan" id="idpemesanan" value="{{ $pemesanan->id }}">
                    <br>
                    <button type="submit" class="btn btn-primary mr-3 mt-2">Perbarui</button>
                </form>


            </div>
            <hr class="mx-auto my-5" style="width: 90%">
        </div>

    </div>

@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    {{-- swal (sweetalert) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateOpsiDetails(idvendor, idlayanan, idjenisbahan) {
            $.ajax({
                url: `/loadlayanan/${idvendor}/${idlayanan}/${idjenisbahan}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#listOpsi').html('');
                    let html = '';


                    if (response.result == 'success') {
                        $('#listdetail').html('<p>Tidak ada opsi yang perlu ditambahkan</p>');
                        if (response.data.opsidetail > 0) {
                            let opsiDetail = {};
                            response.data.opsidetail.forEach(function(item) {
                                const detailid = item.detail.id;

                                if (!opsiDetail[detailid]) {
                                    opsiDetail[detailid] = {
                                        id: detailid,
                                        value: item.detail.value,
                                        opsi: []
                                    };
                                }

                                item.opsi.forEach(function(opsi) {
                                    opsiDetail[detailid].opsi.push({
                                        idopsi: opsi.id,
                                        opsi: opsi.opsi,
                                        biaya_tambahan: opsi.biaya_tambahan
                                    });
                                });
                            });

                            // Construct the HTML for each detail and its options
                            for (let key in opsiDetail) {
                                const detail = opsiDetail[key];
                                html += `
                                     <div class="form-group">
                                        <label class="font-weight-bold" id="detail-${key}">${detail.value}</label>                                         
                                             `;
                                if (detail.opsi.length > 0) {
                                    html +=
                                        `<div class="select-container">
                                             <select class="form-control custom-select px-4" name="opsidetail-${key}"
                                                 id="opsidetail-${key}">`
                                    detail.opsi.forEach(function(option) {
                                        html += `
                                                <option value="${option.idopsi}">${option.opsi}(+Rp. 
                                                ${option.biaya_tambahan})</option>
                                            `;
                                    });
                                    html += `
                                            </select>
                                        <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                                    </div>
                                </div>
                                <br>
                            `;

                                } else {
                                    html = '<p>No Options required.</p>';
                                }

                                $('#listdetail').html(html);
                            }
                        }

                        $('#listharga').html('');
                        $html = '';
                        // console.table(response.data.listharga);
                        response.data.listharga.forEach(function(item) {
                            if (item.jumlah_cetak_maksimum == null) {
                                $html +=
                                    `<li>&gt;${item.jumlah_cetak_minimum - 1} ${item.satuan} = Rp. ${item.harga_satuan}/${item.satuan}</li>`;
                            } else {
                                $html +=
                                    `<li>${item.jumlah_cetak_minimum}–${item.jumlah_cetak_maksimum} ${item.satuan} = Rp. ${item.harga_satuan}/${item.satuan}</li>`;
                            }

                        });
                        var deskripsi = response.data.deskripsi;

                        $('#deskripsi').html(`<p>${deskripsi}</p>`);
                        $('#listharga').html($html);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        function toggleUploadFile(isFileUploadHide) {

            if (isFileUploadHide == false) {

                $('#upload').show();
                $('#file-detail').hide();
            } else {
                $('#upload').hide();
                $('#file-detail').show();

            }

        }


        let file;
        let jumlHalaman;
        let totalLembar;
        let idpemesanan;
        let isFileDeleted = false;

        $(document).ready(function() {
            toggleUploadFile(true);
            idpemesanan = $('#idpemesanan').val();
            fileUrl = `/uploads/${idpemesanan}.pdf`;
            var satuan = $('#satuan').val();
            pdfjsLib.getDocument(fileUrl).promise.then(function(pdf) {
                jumlHalaman = pdf.numPages;
                $('#labelQuantity').text(`Jumlah (total: ${jumlHalaman} ${satuan})`);
            })
            $('#jumlahcopy').prop('disabled', false);

            $('#file-name').text("Dokumen terunggah (PDF)");
            $('#file-detail').val('');


            $(document).on('dragover',"#drop-area", function(event) {
                event.preventDefault(); // Prevent default drag behaviors
            });


            $(document).on('drop',"#drop-area", function(event) {
                event.preventDefault(); // Prevent default drop behaviors
                file = event.originalEvent.dataTransfer.files[0]; // Get dropped files
                if (file.length > 0) {
                    console.log(file);
                    $('#file-name').text('Selected file: ' + file.name);
                    toggleUploadFile(true);
                }
            });


            $(document).on('click',"#drop-area", function() {
                $('#inputfile').click();
            });

            $("#hapus-file").click(function() {
                var html = `<input type="file" name="inputfile" id="inputfile" style="display:none;" accept=".pdf"
                                required>
                            <div id="drop-area" class="border border-primary rounded p-4 text-center">
                                <p class="mb-2">Seret & Jatuhkan dokumen anda atau klik untuk memilih dokumen</p>
                                <img id="imgicon" src="" alt="Upload Icon" class="mb-2"
                                    width="5%" height="5%">
                                <p class="text-muted">Pilih Dokumen</p>
                            </div>
                            <div id="file-error"></div>`;
                $('#upload').html(html);
                $('#imgicon').attr('src', '/assets/downloads/upload.png');
                file = null;
                isFileDeleted = true;
                toggleUploadFile(false);
            });

            // Handle file selection
            $(document).on('change',"#inputfile", function(){
                file = this.files[0]; // Get the selected file
                if (file && file.type === 'application/pdf') {
                    const fileReader = new FileReader();
                    fileReader.onload = function() {
                        const arrPdf = new Uint8Array(this.result);

                        pdfjsLib.getDocument(arrPdf).promise.then(function(pdf) {
                            jumlHalaman = pdf.numPages;
                            $('#file-name').text(
                                `Selected file: ${file.name} (${jumlHalaman} halaman)`
                            );
                            totalLembar = $("#jumlahcopy").val() * jumlHalaman;
                            $("#labelQuantity").text(
                                `Jumlah (total: ${totalLembar} lembar)`);
                        }).catch(function(error) {
                            console.error('Error: ' + error);
                        });
                    };
                    fileReader.readAsArrayBuffer(file);
                    $('#jumlahcopy').prop('disabled', false);

                    toggleUploadFile(true);
                } else {
                    $('#file-error').text(
                        'Silahkan masukkan file dengan format "PDF"!'); // Handle no file selected
                }
            });

            $('#lihat-file').on('click', function() {
                let fileUrl = `/uploads/${idpemesanan}.pdf`;
                window.open(fileUrl, '_blank');
            });

            $('#jumlahcopy').on('change', function() {
                if (Number.isInteger(parseInt($("#jumlahcopy").val())) && jumlHalaman != null) {
                    let value = $("#jumlahcopy").val();
                    totalLembar = value * jumlHalaman;
                    $("#labelQuantity").text(`Jumlah (total: ${totalLembar} lembar)`);

                }
            });
            $('#jenisbahan').on('change', function() {
                idvendor = $('#idvendor').val();
                idlayanan = $('#idlayanan').val();
                idjenisbahan = $('#jenisbahan').val();

                updateOpsiDetails(idvendor, idlayanan, idjenisbahan);

            });


            $('#form').on('submit', function(event) {
                event.preventDefault();
                if (isFileDeleted) {
                    if (file && file.type == 'application/pdf') {
                        const idjenisbahan = $('#jenisbahan').val();
                        let idopsidetail = [];
                        const opsidetailElements = $('[id^="opsidetail-"]');

                        for (let i = 0; i < opsidetailElements.length; i++) {
                            idopsidetail.push($(opsidetailElements[i]).val());
                        }
                        const jumlahCopy = $("#jumlahcopy").val();;
                        const totalQuantity = totalLembar;
                        const catatan = $("#catatan").val();
                        const jenis_bahan_cetaks_id = $('#jenisbahan').val();
                        const vendors_id = $('#idvendor').val();

                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('ischanged', isFileDeleted);
                        formData.append('jumlahcopy', jumlahCopy);
                        formData.append('jumlah', totalQuantity);
                        formData.append('jenis_bahan_cetaks_id', jenis_bahan_cetaks_id);
                        formData.append('vendors_id', vendors_id);
                        formData.append('idopsidetail', idopsidetail);
                        formData.append('catatan', catatan);


                        // Make the AJAX POST request
                        $.ajax({
                            url: '/updatepesanan',
                            type: 'POST',
                            contentType: false,
                            processData: false,
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: "Pesanan berhasil diperbarui",
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/cart/orders/' +
                                            vendors_id;
                                    }
                                });

                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                                $('#response').text('Error: ' + error);
                            }
                        });
                    } else {
                        $("#error-message").html(`<div class="alert alert-danger" role="alert">
                    File harus berupa PDF!
                    </div>`);
                    }
                }
                else{
                    $('#fg-input-file').html("");
                    const idjenisbahan = $('#jenisbahan').val();
                    let idopsidetail = [];
                    const opsidetailElements = $('[id^="opsidetail-"]');
                    for (let i = 0; i < opsidetailElements.length; i++) {
                        idopsidetail.push($(opsidetailElements[i]).val());
                    }
                    const jumlahCopy = $("#jumlahcopy").val();;
                    const totalQuantity = totalLembar;
                    const catatan = $("#catatan").val();
                    const jenis_bahan_cetaks_id = $('#jenisbahan').val();
                    const vendors_id = $('#idvendor').val();
                    const formData = new FormData();
                    formData.append('file', '');
                    formData.append('ischanged', 'false');
                    formData.append('jumlahcopy', jumlahCopy);
                    formData.append('jumlah', totalQuantity);
                    formData.append('jenis_bahan_cetaks_id', jenis_bahan_cetaks_id);
                    formData.append('vendors_id', vendors_id);
                    formData.append('idopsidetail', idopsidetail);
                    formData.append('catatan', catatan);
                    // Make the AJAX POST request
                    $.ajax({
                        url: '/updatepesanan',
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: "Pesanan berhasil diperbarui",
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/cart/orders/' +
                                        vendors_id;
                                }
                            });

                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            $('#response').text('Error: ' + error);
                        }
                    });
                    
                }

            });
        });
    </script>
@endsection
