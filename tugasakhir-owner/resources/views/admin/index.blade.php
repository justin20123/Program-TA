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
                Are you sure you want to delete this admin?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="/delete" method="POST">
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

<h3 class="text-center p-4">List Admin</h1>
  
<table class="table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($admins as $a)
    <tr class="justify-content-between">
        <td scope="row">
            {{$a->nama}}
            
        </td>
        <td>
            <ul class="list-inline justify-content-between">

                
                <li class="list-inline-item">
                    <a href="/admin/{{$a->id}}/edit" class="btn btn-primary">Edit Admin</a>
                    <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
                        data-id="{{$a->id}}" 
                        data-target="#confirmDeleteModal">Delete
                    </button>
                </li>
            </ul>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
<a href="{{ route('admin.create') }}" class="btn btn-success">Tambah Admin</a>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {

    let deleteId;
    let deleteVendorId; 

    $(document).on('click', '.delete-button', function() {
        deleteId = $(this).data('id');


        $("#hiddens").html(
            `
            <input type="hidden" name="id" value="${deleteId}">
        
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