<nav class="navbar navbar-light bg-light w-100">
    <a class="navbar-link">&ensp;</a>
    <a class="navbar-brand">Logo</a>
    <div class="navbar-item justify-content-between">
        <a href="/" class="navbar-link">Home</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/cart" class="navbar-link">Cart</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/pengantar" class="navbar-link">Pengantar</a>
        <a class="navbar-link">&ensp;</a>
        <a href="/pegawai" class="navbar-link">Pegawai</a>
    </div>
    <div class="navbar-brand">Hak Akses: Manajer</div>
    <div class="navbar-brand">Saldo: Rp 500.000</div>
    <form class="form-inline">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Keuangan
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="#">Top Up</a>
              <a class="dropdown-item" href="#">Transfer Bank</a>
              <a class="dropdown-item" href="#">History Keuangan</a>
            </div>
          </div>
      </form>
    <a class="navbar-link">&ensp;</a>
</nav>

<nav aria-label="breadcrumb" style="padding-left: 5%">
    @yield('breadcrumb')
</nav>
