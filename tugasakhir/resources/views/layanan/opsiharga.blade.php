@extends ("layanan.optiontemplate")
@section ("breadcrumb")
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
    <li class="breadcrumb-item active" aria-current="page">Opsi Layanan</li>
  </ol>
@endsection
@section("title")
<a href="/layanans/{{ $detail->id_vendor }}/details/{{ $detail->id_layanan_cetak }}" class="px-4 pt-3 text-black">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="h3 px-4 py-2">Harga {{ $detail->nama_jenis_bahan }}</div>

@endsection

@section("modal")
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah anda yakin ingin menghapus harga ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="{{ route('harga.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div id="hiddens">

                    </div>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section("buttontambah")
<a href="../../opsiharga/create/{{ $detail->id_jenis_bahan }}" class="btn btn-success">Tambah Harga</a>
@endsection 
@section("tableitem")
    @foreach($hargas as $h)
    <tr>
        <td scope="row">{{$h->jumlah_cetak_minimum}}-{{$h->jumlah_cetak_maksimum}} lembar - Rp {{$h->harga_satuan}}/{{$detail->satuan}}</td>
        <td>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="../../opsiharga/edit/{{$h->id}}" class="btn btn-primary">Edit Opsi</a>
                </li>
                <li class="list-inline-item">
                    <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
                        data-id="{{$h->id}}" 
                        data-jenisbahan="{{ $detail->id_jenis_bahan }}" 
                        data-toggle="modal" 
                        data-target="#confirmDeleteModal">Delete
                    </button>
                </li>
            </ul>
        </td>
    </tr>
    @endforeach
@endsection 

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    let deleteHargaId;
    let deleteJenisBahanId; 

    $(document).on('click', '.delete-button', function() {
        deleteHargaId = $(this).data('id');
        deleteJenisBahanId = $(this).data('jenisbahan');

        $("#hiddens").html(
            `
            <input type="hidden" name="idharga" value="${deleteHargaId}">
            <input type="hidden" name="idjenisbahan" value="${deleteJenisBahanId}">
            `
        );

        $('#confirmDeleteModal').modal('show');
    });
});
</script>
@endsection