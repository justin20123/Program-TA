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


                <!-- Dropdowns for Paper Type and Lamination -->
                <div class="form-group">
                    <label for="paperType" class="font-weight-bold">Pilih Jenis dan Bahan</label>
                    <select id="paperType" class="form-control">
                        <option>HVS 80 Gsm A4 BW Hitam Putih</option>
                        <!-- Other options can go here -->
                    </select>
                </div>
                <br>
                <div class="form-group">
                    <label for="lamination" class="font-weight-bold">Tambahan Laminasi</label>
                    <select id="lamination" class="form-control">
                        <option>Tidak Dilaminasi (+ Rp 0)</option>
                        <!-- Other options can go here -->
                    </select>
                </div>
                <br>
                <!-- Quantity Selector -->
                <div class="form-group d-flex align-items-center">
                    <label for="quantity" class="font-weight-bold mr-3">Jumlah</label>
                    <input type="number" id="quantity" class="form-control w-25" min="1" value="1">
                </div>
                <br>

                <!-- Add to Cart and Cart Buttons -->
                <div class="form-group d-flex">
                    <button class="btn btn-primary mr-3">Add To Cart</button>
                    <button class="btn btn-outline-secondary">Cart</button>
                </div>

                <!-- Notes and File Upload -->
                <div class="form-group mt-4">
                    <label for="notes" class="font-weight-bold">Catatan</label>
                    <textarea id="notes" class="form-control" rows="3" placeholder="Your Message"></textarea>
                </div>
                <br>

                <div class="form-group">
                    <label for="fileUpload" class="font-weight-bold">Upload File</label>
                    <input type="file" id="fileElem" style="display:none;" accept=".pdf">
                    <div id="drop-area" class="border border-primary rounded p-4 text-center">
                        <p class="mb-2">Drag & Drop your files here or click to browse</p>
                        <img src="https://via.placeholder.com/100" alt="Upload Icon" class="mb-2">
                        <p class="text-muted">Select Files</p>
                    </div>
                </div>
            @endsection

            @section('script')
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function() {
                        // Prevent default drag behaviors


                        $("#drop-area").on('drop', function(event) {
                            event.preventDefault(); // Prevent default drop behaviors
                            const files = event.originalEvent.dataTransfer.files; // Get dropped files
                            if (files.length > 0) {
                                console.log("Files dropped:", files); // Log the dropped files
                            }
                        });

                        // Click event for the drop area to open file explorer
                        $("#drop-area").click(function() {
                            $('#fileElem').click(); // This will open the file explorer
                        });

                        // Handle file selection
                        $('#fileElem').on('change', function() {
                            const files = this.files; // Get the selected files
                            if (files.length > 0) {
                                console.log("Files selected:", files[0]['name']); // Log the files for demonstration
                            }
                        });
                    });
                </script>
            @endsection
