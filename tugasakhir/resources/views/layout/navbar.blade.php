<nav class="navbar navbar-light bg-light w-100">
    <a class="navbar-link">&ensp;</a>
    <div class="navbar-brand"><img src="{{ asset('assets/logo/printajalogo.png') }}" width="75px" height="75px"></div>
    <div class="navbar-item justify-content-between">
        <a href="/" class="navbar-link">Layanan</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/pesanancetak" class="navbar-link">Pesanan</a>
        <a class="navbar-link">&ensp;</a>
        @if (Auth::user()->role=="manajer")
          <a href="/pengantar" class="navbar-link">Pengantar</a>
          <a class="navbar-link">&ensp;</a>
          <a href="/pegawai" class="navbar-link">Pegawai</a>
        @endif
    </div>
    <div class="navbar-brand">Hak Akses: 
      @if(Auth::check())
      {{ ucfirst(Auth::user()->role) }}
  
      @endif
    </div>
    <div class="navbar-brand">
      @if (Auth::user() && Auth::user()->role=="manajer")
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
            @if (Auth::user() && Auth::user()->role=="manajer")
              <li><a href="/tarikdana">Tarik Dana</a></li>
            @endif
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

