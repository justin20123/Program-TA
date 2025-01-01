@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item" aria-current="page">Vendors</li>
    <li class="breadcrumb-item" aria-current="page">Orders</li>
    <li class="breadcrumb-item active" aria-current="page">Order Detail</li>
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
                Are you sure you want to delete this pegawai?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="" method="POST">
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

<h3 class="text-center p-4">Pegawai {{$datavendor->nama}}</h1>
  
<table class="table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Item</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pegawais as $p)
    <tr class="justify-content-between">
        <td>
            <img class="od-image" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1925379617b%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1925379617b%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22107.1937484741211%22%20y%3D%2296.24000034332275%22%3E286x180%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="" srcset="">        
        </td>
        <td scope="row">
            {{$p->nama}}
            
        </td>
        <td>
            <ul class="list-inline justify-content-between">

                
                <li class="list-inline-item">
                    <a href="/editpegawai/{{$p->id}}" class="btn btn-primary">Edit Pegawai</a>
                    <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
                    data-id="{{$p->id}}" 
                    data-vendor="{{$datavendor->id }}"  
                    data-toggle="modal" 
                    data-target="#confirmDeleteModal">Delete
        </button>
                </li>
            </ul>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
<a href="/addpegawai/{{$datavendor->id}}" class="btn btn-success">Tambah Pegawai</a>
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
        const deleteUrl = "/deletepegawai/"+ deleteId;

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