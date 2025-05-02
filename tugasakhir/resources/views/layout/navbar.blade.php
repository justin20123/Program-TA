<nav class="navbar navbar-light bg-light w-100">
    <a class="navbar-link">&ensp;</a>
    <div class="navbar-brand"><img src="{{ asset('assets/logo/printajalogo.png') }}" width="75px" height="75px"></div>
    <div class="navbar-item justify-content-between">
        <a href="/" class="navbar-link">Layanan</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/pesanancetak" class="navbar-link">Pesanan</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/pengantar" class="navbar-link">Pengantar</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/pegawai" class="navbar-link">Pegawai</a>
    </div>
    <div class="navbar-brand">Hak Akses: 
      @if(Auth::check())
      {{ ucfirst(Auth::user()->role) }}
  
      @endif
    </div>
    <div class="navbar-brand">
      @if (Auth::user())
        <div>
            Saldo: Rp. {{ number_format(Auth::user()->saldo, 0, '.', ',') }}
        </div>
        @endif
    </div>
    <form class="form-inline">
      <div class="dd">
        <span>Menu</span>
        <div class="dd-content">
          <ul>
            <li><a href="/masukdana">Masukkan Dana</a></li>
            <li><a href="/logout">Keluar</a></li>
          </ul>
        </div>
      </div>
      </form>
    <a class="navbar-link">&ensp;</a>
</nav>

<nav aria-label="breadcrumb" style="padding-left: 5%">
    @yield('breadcrumb')
</nav>

