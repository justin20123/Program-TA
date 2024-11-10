@extends('layout.sneat')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
    </ol>
@endsection
@section('menu')
    <div class="container py-5">
        <!-- Main Row Layout -->
        <div class="row">
            <!-- Left Side: Image Gallery and Price List -->
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

                <h2 class="h4 font-weight-bold">Harga (1 lembar = 1 pcs)</h2>
                <ul class="list-unstyled text-muted mb-0">
                    <li>1–20 lembar = Rp. 400/lembar (1 hari)</li>
                    <li>21–60 lembar = Rp. 350/lembar (1 hari)</li>
                    <li>61–150 lembar = Rp. 300/lembar (1 hari)</li>
                    <li>151–300 lembar = Rp. 250/lembar (1 hari)</li>
                    <li>&gt;300 lembar = Rp. 200/lembar (1 hari)</li>
                </ul>

            </div>

            <!-- Right Side: Product Details -->
            <div class="col-md-8">
                <h1 class="display-4">Fotokopi</h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning">
                        <!-- 5 Star Rating Display -->
                        ★★★★★
                    </div>
                    <span class="ml-2 text-muted">(5 Customer Review)</span>
                </div>

                <form action="" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="paperType" class="font-weight-bold">Pilih Jenis dan Bahan</label>
                        <div class="select-container">
                            <select class="form-control custom-select px-4" name="layanans" id="layanans">
                                <option value="">HVS 80 Gsm A4 BW Hitam Putih</option>
                            </select>
                            <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="lamination" class="font-weight-bold">Tambahan Laminasi</label>
                        <div class="select-container">
                            <select class="form-control custom-select px-4" name="layanans" id="layanans">
                                <option value="">Tidak Dilaminasi (+ Rp 0)</option>
                            </select>
                            <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                        </div>  
                    </div>
                    <br>
                    <!-- Quantity Selector -->
                    <div class="form-group d-flex align-items-center">
                        <label for="quantity" class="font-weight-bold mr-3">Jumlah</label>
                        <input type="number" id="quantity" class="form-control w-25" min="1" value="1" required>
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
                        <label for="notes" class="font-weight-bold">Catatan</label>
                        <textarea id="notes" class="form-control" rows="3" placeholder="Catatan" required></textarea>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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
        let file;
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
                if (file) {
                    console.log(file);
                    $('#file-name').text('Selected file: ' + file.name);
                    toggleUploadFile(true);
                } else {
                    $('#file-name').text('No file selected'); // Handle no file selected
                }
            });

            $('#lihat-file').on('click', function() {
                if (file) {
                    const fileURL = URL.createObjectURL(file); // Create a URL for the file
                    window.open(fileURL); // Open the PDF in a new window/tab
                }
            });
        });
    </script>
@endsection
