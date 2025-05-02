<nav class="navbar navbar-light bg-light w-100">
  <a class="navbar-link">&ensp;</a>
  <div class="navbar-brand"><img src="{{ asset('assets/logo/printajalogo.png') }}" width="75px" height="75px"></div>

  <div class="navbar-brand">Hak Akses: Pengantar
  </div>
  <div class="navbar-brand"></div>    
  <div class="navbar-brand"> </div>    
  <div class="navbar-item">
    <a href="/logout" class="btn-logout">Logout</a>
  </div>
  <a class="navbar-link">&ensp;</a>
</nav>

<nav aria-label="breadcrumb" style="padding-left: 5%">
  @yield('breadcrumb')
</nav>
