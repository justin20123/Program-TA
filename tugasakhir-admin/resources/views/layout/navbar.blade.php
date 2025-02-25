<nav class="navbar navbar-light bg-light w-100">
    <a class="navbar-link">&ensp;</a>
    <a class="navbar-brand">Logo</a>
    <div class="navbar-item justify-content-between">
        <a href="/" class="navbar-link">Pemblokiran</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/verifikasi" class="navbar-link">Verifikasi</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/lepasblokir" class="navbar-link">Lepas Blokir</a>

    </div>
    <div class="navbar-brand">Hak Akses: 
      @if(Auth::check())
      {{ ucfirst(Auth::user()->role) }}
  
      @endif
    </div>
    <div class="navbar-brand">Saldo: Rp 500.000</div>
    <div class="navbar-item">
      <a href="/logout" class="btn-logout">Logout</a>
    </div>

    <a class="navbar-link">&ensp;</a>
</nav>

<nav aria-label="breadcrumb" style="padding-left: 5%">
    @yield('breadcrumb')
</nav>
