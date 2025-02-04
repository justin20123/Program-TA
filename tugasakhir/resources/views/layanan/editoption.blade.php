  @extends ("layout.sneat")
  @section('breadcrumb')
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="#">Vendors</a></li>
          <li class="breadcrumb-item" aria-current="page">Layanan</li>
          <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
          <li class="breadcrumb-item" aria-current="page">Opsi Layanan</li>
          <li class="breadcrumb-item active" aria-current="page">Create</li>
      </ol>
  @endsection
  @section('menu')
      <a href="{{ url()->previous() }}" class="px-4 py-2 text-black">
          <i class="fas fa-arrow-left"></i>
      </a>
      <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
          aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="editModalLabel">Edit Detail</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <form id="editForm">
                          <div class="form-group">
                              <label for="editInput">Detail</label>
                              <input type="text" class="form-control" id="editInput" required>
                          </div>
                          <input type="hidden" id="detailId">
                      </form>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                  </div>
              </div>
          </div>
      </div>

      <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
          aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapusan</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      Apakah Anda yakin ingin menghapus detail ini dan semua opsinya?
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Batalkan</button>
                      <form id="deleteForm" action="{{ route('detail.destroy') }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <div id="hiddens">

                          </div>
                          <button type="submit" class="btn btn-danger">Hapus</button>
                      </form>
                  </div>
              </div>
          </div>
      </div>
      <div class="h3 px-4 py-2">Edit Opsi {{ $detail->value }}: {{ $opsi_detail->opsi }}</div>
      <form action="{{ route('opsidetail.update') }}" method="post">
          @csrf
          @method('PUT')
          <div class="h3 px-4 py-2">
              <div class="form-group">
                  <label for="">Opsi</label>
                  <input type="text" class="form-control" name="opsi" id="" aria-describedby="helpId"
                      placeholder="" value="{{ $opsi_detail->opsi }}">
              </div>
          </div>
          <div class="h3 px-4 py-2">
              <div class="form-group">
                  <label for="">Tambahan Biaya</label>
                  <input type="number" class="form-control" name="biaya_tambahan" id="" aria-describedby="helpId"
                      placeholder="" value="{{ $opsi_detail->biaya_tambahan }}">
              </div>
          </div>
          <div class="h3 px-4 py-2">
              <div class="form-group">
                  <label for="tipe">Tipe</label>
                  <select class="form-control" name="tipe" id="tipe">

                      <option value="tambahan">Tambahan (harga ditambahkan pada total)</option>
                      <option value="satuan">Satuan (harga ditambahkan per satuan)</option>
                      <option value="jumlah">Tiap Jumlah (contoh: 1 buku berisi 100 lembar, maka ditambahkan sekali per
                          buku)</option>

                  </select>
              </div>
          </div>
          <div class="h3 px-4 py-2">
              <div class="form-group">
                  <input type="checkbox" name="khusus">
                  <label>
                      <h6>Ubah hanya untuk jenis bahan ini</h6>
                  </label>

              </div>
          </div>


          <input type="hidden" name="id_detail" value="{{ $opsi_detail->detail_cetaks_id }}">

          <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
              <input type="submit" value="Submit" class="btn btn-success">
          </div>
      </form>
  @endsection
