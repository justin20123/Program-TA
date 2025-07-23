@extends ("layout.sneat")
@section('breadcrumb')
    <ol class="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        <li class="breadcrumb-item" aria-current="page">Pegawai</li>
          <li class="breadcrumb-item active" aria-current="page">Ubah Pegawai</li>
        </ol>
    </ol>
@endsection
@section('menu')
    <div class="h3 px-4 py-2">Ubah Pegawai: {{ $pegawai->nama }}</div>
    <form action="{{ route('pegawai.update', $pegawai->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="h3 px-4 py-2">
            <div class="form-group">
                <label for="">Nama</label>
                <input type="text" class="form-control" name="nama" id="" aria-describedby="helpId"
                    placeholder="" value="{{ $pegawai->nama }}">
            </div>
        </div>
        <div class="h3 px-4 py-2">
          <div class="form-group">
            <label for="">Nomor Telepon</label>
            <input type="tel" name="nomor_telepon" class="form-control" placeholder="Nomor Telepon" value="{{ $pegawai->nomor_telepon }}"  required  pattern="[0-9]*" inputmode="numeric">
        
          </div>
        </div>
        <input type="hidden" name="idvendor" value="{{ $pegawai->vendors_id }}">
        <div style="display: flex; justify-content: center;" class="pb-5 pt-2">
          <input type="submit" value="Kirim" class="btn btn-success">
        </div>    
      </form>
      @if (session('error'))
                <p class="text text-danger">{{ session('error') }}</p>
            @endif
@endsection
