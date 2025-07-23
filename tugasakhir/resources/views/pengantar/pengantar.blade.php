@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengantar</li>
</ol>

@endsection
@section('menu')
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
                Apakah anda yakin ingin menghapus pengantar ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="POST">
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

<h3 class="text-center p-4">Pengantar {{$data_vendor->nama}}</h1>
  
<table class="table">
    <thead>
        <tr>
            <th>Pengantar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pengantars as $p)
    <tr class="justify-content-between">
        <td scope="row">
            {{$p->nama}}
            
        </td>
        <td>
            <ul class="list-inline justify-content-between">

                
                <li class="list-inline-item">
                    <a href="/editpengantar/{{$p->id}}" class="btn btn-primary">Ubah Pengantar</a>
                    <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
                    data-id="{{$p->id}}" 
                    data-vendor="{{$data_vendor->id }}"  
                    data-toggle="modal" 
                    data-target="#confirmDeleteModal">Hapus
        </button>
                </li>
            </ul>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
<a href="/addpengantar/{{$data_vendor->id}}" class="btn btn-success">Tambah Pengantar</a>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {

    let deleteId;
    let deleteVendorId; 

    $(document).on('click', '.delete-button', function() {
        deleteId = $(this).data('id');
        deleteVendorId = $(this).data('vendor');
        const deleteUrl = "/deletepengantar/"+ deleteId;

        $("#deleteForm").attr("action", deleteUrl);

        $("#hiddens").html(
            `
            <input type="hidden" name="idvendor" value="${deleteVendorId}">
        
            `
        );

        $('#confirmDeleteModal').modal('show');
    });
    $('#confirmDeleteButton').on('click', function() {
    $('#deleteForm').submit(); // This submits the form
});
});
</script>
@endsection