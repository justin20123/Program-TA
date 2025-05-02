@extends ("layout.sneat")
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
    </ol>
@endsection
@section('menu')
<a href="../" class="px-4 pt-3 text-black">
    <i class="fas fa-arrow-left"></i>
</a> 
    <div class="h3 px-4 py-2">Masukkan Dana</div>
    <form action="{{ route('masukdana') }}" method="post">
        @csrf
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Nominal yang ingin ditambahkan</label>
                <input type="number" class="form-control" name="nominal" id="" aria-describedby="helpId"
                    placeholder="">
            </div>
        </div>
        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
            <input type="submit" valu e="Submit" class="btn btn-success">
          </div>
    </form>
@endsection
