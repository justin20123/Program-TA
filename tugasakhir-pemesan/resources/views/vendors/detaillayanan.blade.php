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
        <!-- Main Row Layout -->
        <div class="row">
            <div class="col-md-4">
                <!-- Image Gallery -->
                <div class="mb-4">
                    <img src="https://picsum.photos/300/200" alt="Main Product" class="img-fluid rounded">
                </div>
                <!-- Thumbnails -->
                <div class="d-flex mb-4">
                    <img src="https://picsum.photos/300/200" class="img-thumbnail px-2" style="width: 75px; height: 75px;"
                        alt="Thumbnail 1">
                    <img src="https://picsum.photos/300/200" class="img-thumbnail px-2" style="width: 75px; height: 75px;"
                        alt="Thumbnail 2">
                    <img src="https://picsum.photos/300/200" class="img-thumbnail px-2" style="width: 75px; height: 75px;"
                        alt="Thumbnail 3">
                </div>

                <!-- Price List Section -->

                <h2 class="h4 font-weight-bold">Harga (1 {{ $layanan->satuan }} = {{ $layanan->kesetaraan_pcs }} pcs)</h2>
                <ul class="list-unstyled text-muted mb-0">
                    @foreach ($hargacetaks as $h)
                        <li>{{ $h->jumlah_cetak_minimum }}–{{ $h->jumlah_cetak_maksimum }} {{ $layanan->satuan }} = Rp.
                            {{ $h->harga_satuan }}/{{ $layanan->satuan }}</li>
                    @endforeach
                </ul>
                <input type="hidden" id="idvendor" value="{{ $jenisbahan[0]->idvendor }}">
                <input type="hidden" id="idlayanan" value="{{ $layanan->id }}">

            </div>

            <!-- Right Side: Product Details -->
            <div class="col-md-8">
                <h1 class="display-4">{{ $layanan->nama }}</h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning">
                        <!-- 5 Star Rating Display -->
                        ★★★★★
                    </div>
                    <span class="ml-2 text-muted">(5 Customer Review)</span>
                </div>

                <form action="" method="post" id='form'>
                    @csrf
                    <div class="form-group">
                        <label for="paperType" class="font-weight-bold">Pilih Jenis dan Bahan</label>
                        <div class="select-container">
                            <select class="form-control custom-select px-4" name="jenisbahan" id="jenisbahan">
                                @foreach ($jenisbahan as $jb)
                                    <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                @endforeach
                            </select>
                            <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                        </div>
                    </div>
                    <br>
                    <div id="listdetail">
                        @foreach ($opsidetail as $key => $od)
                            <div class="form-group">
                                <label class="font-weight-bold">{{ $od['detail']->value }}</label>
                                <div class="select-container">
                                    <select class="form-control custom-select px-4" name="opsidetail-{{ $key }}"
                                        id="opsidetail-{{ $key }}">
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
                    </div>

                    <!-- Quantity Selector -->
                    <div class="form-group">
                        <label id="labelQuantity" class="font-weight-bold mr-3">Jumlah (upload file terlebih
                            dahulu!)</label>

                        <input type="number" id="quantity" class="form-control w-25" min="1" value="1"
                            required disabled>
                    </div>
                    <br>

                    <!-- Add to Cart and Cart Buttons -->
                    <div class="form-group d-flex">

                        <button class="btn btn-outline-secondary">Cart</button>
                    </div>
                    <br>
                    <hr>
                    <!-- Notes and File Upload -->
                    <div class="form-group mt-4">
                        <label for="catatan" class="font-weight-bold">Catatan</label>
                        <textarea id="catatan" class="form-control" rows="3" placeholder="Catatan"></textarea>
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="fileUpload" class="font-weight-bold">Upload File</label>
                        <div id="upload">
                            <input type="file" id="fileElem" style="display:none;" accept=".pdf" required>
                            <div id="drop-area" class="border border-primary rounded p-4 text-center">
                                <p class="mb-2">Drag & Drop file anda atau klik untuk memilih file</p>
                                <img src="{{ asset('assets/downloads/upload.png') }}" alt="Upload Icon" class="mb-2"
                                    width="5%" height="5%">
                                <p class="text-muted">Select Files</p>
                            </div>
                            <div id="file-error"></div>
                        </div>
                        <div id="file-detail">
                            <div class="file-name-container d-flex justify-content-between">
                                <div id="file-name"></div>
                                <div id="hapus-file" class="btn btn-danger">X</div>
                            </div>


                            <button id="lihat-file" class="btn btn-primary">Preview PDF</button>




                        </div>

                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary mr-3 mt-2">Add To Cart</button>
                </form>

                <!-- Dropdowns for Paper Type and Lamination -->

            </div>
            <hr class="mx-auto my-5" style="width: 90%">
        </div>
    </div>
@endsection


@section('script')
    {{-- pdf.js --}}
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
                    $('#listOpsi').html(''); // Clear the existing options
                    let html = '';

                    if (response.result === 'success' && Array.isArray(response.data)) {
                        let opsiDetail = {};

                        response.data.forEach(function(item) {
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
                                                 id="opsidetail-{{ $key }}">`
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
                }
            });
        }

        function toggleUploadFile(isFileUploadHide) {

            if (isFileUploadHide == false) {
                console.log(file);
                $('#upload').show();
                $('#file-detail').hide();
            } else {
                $('#upload').hide();
                $('#file-detail').show();

            }

        }

        function uploadFile(idpemesanan, idvendor) {
            const formData = new FormData();
            formData.append('fileInput', file);
            formData.append('idpemesanan', idpemesanan);

            $.ajax({
                url: '/uploadfilepesanan',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to the vendor page
                            window.location.href = '/vendor/' + idvendor;
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $('#response').text('Error: ' + error);
                }
            });
        }

        let file;
        let jumlHalaman;
        let totalLembar;
        $(document).ready(function() {
            if (!file) {
                toggleUploadFile(false);
            }

            $("#drop-area").on('dragover', function(event) {
                event.preventDefault(); // Prevent default drag behaviors
            });


            $("#drop-area").on('drop', function(event) {
                event.preventDefault(); // Prevent default drop behaviors
                file = event.originalEvent.dataTransfer.files[0]; // Get dropped files
                if (file.length > 0) {
                    console.log(file);
                    $('#file-name').text('Selected file: ' + file.name);
                    toggleUploadFile(true);
                }
            });


            $("#drop-area").click(function() {
                $('#fileElem').click();
            });

            $("#hapus-file").click(function() {
                file = null;
                toggleUploadFile(false);
            });

            // Handle file selection
            $('#fileElem').on('change', function() {
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
                            totalLembar = $("#quantity").val() * jumlHalaman;
                            $("#labelQuantity").text(
                                `Jumlah (total: ${totalLembar} lembar)`);
                        }).catch(function(error) {
                            console.error('Error: ' + error);
                        });
                    };
                    fileReader.readAsArrayBuffer(file);
                    $('#quantity').prop('disabled', false);

                    toggleUploadFile(true);
                } else {
                    $('#file-error').text(
                        'Silahkan masukkan file dengan format "PDF"!'); // Handle no file selected
                }
            });

            $('#lihat-file').on('click', function() {
                if (file) {
                    const fileURL = URL.createObjectURL(file); // Create a URL for the file
                    window.open(fileURL); // Open the PDF in a new window/tab
                }
            });

            $('#quantity').on('change', function() {
                if (Number.isInteger(parseInt($("#quantity").val())) && jumlHalaman != null) {
                    let value = $("#quantity").val();
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
                event.preventDefault(); // Prevent form submission
                if (file && file.type === 'application/pdf') {
                    const idjenisbahan = $('#jenisbahan').val();
                    let idopsidetail = [];
                    const opsidetailElements = $('[id^="opsidetail-"]');

                    for (let i = 0; i < opsidetailElements.length; i++) {
                        idopsidetail.push($(opsidetailElements[i]).val());
                    }
                    const totalQuantity = totalLembar;
                    const catatan = $("#catatan").val();
                    const jenis_bahan_cetaks_id = $('#jenisbahan').val();
                    const vendors_id = $('#idvendor').val();

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('jumlah', totalQuantity);
                    formData.append('jenis_bahan_cetaks_id', jenis_bahan_cetaks_id);
                    formData.append('vendors_id', vendors_id);
                    formData.append('idopsidetail', idopsidetail);
                    formData.append('catatan', catatan);

                    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

                    // Make the AJAX POST request
                    $.ajax({
                        url: '/submitpesanan',
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
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirect to the vendor page
                                    window.location.href = '/vendor/' + idvendor;
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
            });
        });
    </script>
@endsection
