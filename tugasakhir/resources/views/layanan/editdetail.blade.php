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
<a href="{{ url()->previous() }}" class="px-4 py-2 text-black">
    <i class="fas fa-arrow-left"></i>
</a>
<a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
<div class="h3 px-4 py-2">Pilih Jenis dan Bahan</div>
<form action="" method="post">
    <div class="form-group">
        <label for="">Nama Detail</label>
        <input type="text"
          class="form-control" name="" id="" aria-describedby="helpId" placeholder="Masukkan nama detail baru">
    </div>
    <input type="submit" value="Submit" class="btn btn-success">
</form>
<br>
<br>
@endsection
@section("buttontambah")
<a href="editdetail/create/option" class="btn btn-success">Tambah Detail</a>
@endsection 
@section("tableitem")
    @for ($i = 0; $i < 10; $i++)
    <tr>
        <td scope="row">Item {{$i}}</td>
        <td>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="editdetail/edit/option" class="btn btn-primary">Edit Opsi</a>
                </li>
                <li class="list-inline-item">
                    <a href="delete" class="btn btn-danger">Delete</a>
                </li>
            </ul>
        </td>
    </tr>
    @endfor
@endsection 