@extends ("layanan.optiontemplate")
@section ("breadcrumb")
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item" aria-current="page">Detail Layanan</li>
    <li class="breadcrumb-item active" aria-current="page">Opsi Layanan</li>
  </ol>
@endsection
@section("title")
<a href="{{ url()->previous() }}" class="px-4 py-2 text-black">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="h3 px-4 py-2">Opsi Detail: {{ $detail->value }}</div>

@endsection

@section("modal")
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapuskan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah anda yakin ingin menghapus opsi ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" action="{{ route('opsidetail.destroy') }}" method="POST">
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
@endsection
@section("buttontambah")
<a href="/addopsidetail/{{$detail->id}}" class="btn btn-success">Tambah Opsi</a>
@endsection 
@section("tableitem")
    @foreach($opsiDetails as $od)
    <tr>
        @if($od->biaya_tambahan == 0)
            <td scope="row">{{ $od->opsi }}</td>
        @else
            <td scope="row">{{ $od->opsi }} (+ Rp. {{ $od->biaya_tambahan }}/{{$od->tipe}})</td>
        @endif
        <td>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="/opsidetail/edit/{{$od->id}}" class="btn btn-primary">Ubah Opsi</a>
                </li>
                <li class="list-inline-item">
                    <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
                        data-id="{{$od->id}}" >Hapus
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

    $(document).on('click', '.delete-button', function() {
        deleteOpsiDetailId = $(this).data('id');
        $('#hiddens').html(`
        <input type="hidden" name="id" value="${deleteOpsiDetailId}">
        `);
        $('#confirmDeleteModal').modal('show');
    });
});
</script>
@endsection