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
    <div class="h3 px-4 py-2">Edit pengantar: {{ $pengantar->nama }}</div>
    <form action="{{ route('pengantar.update', $pengantar->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Nama</label>
                <input type="text" class="form-control" name="nama" id="" aria-describedby="helpId"
                    placeholder="" value="{{ $pengantar->nama }}">
            </div>
        </div>
        <div class="h3 px-4 py-2">
          <div class="form-group">
            <label for="">Nomor Telepon</label>
            <input type="tel" name="nomor_telepon" class="form-control" placeholder="Nomor Telepon" value="{{ $pengantar->nomor_telepon }}"  required  pattern="[0-9]*" inputmode="numeric">
        
          </div>
        </div>
        <input type="hidden" name="idvendor" value="{{ $pengantar->vendors_id }}">
        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
          <input type="submit" value="Submit" class="btn btn-success">
        </div>    
      </form>
      @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
@endsection
