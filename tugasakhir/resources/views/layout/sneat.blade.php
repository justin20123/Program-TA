<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <style>

.rating-images {
    display: flex;
    flex-direction: row;
}

.rating-images img {
    width: 40px;
    height: 40px;
    margin-right: 10px;
}
.select-container {
  position: relative;
}

.caret-down-icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}
.od-image{
    width: 8rem;
    height: 5rem;
    object-fit: cover;
}
.stepper {
  display: flex;
  justify-content: space-between;
  position: relative;
}

.step-item {
  flex: 1;
  text-align: center;
  position: relative;
}

.step-item.active {
  color: #337ab7;
}

.step-item.active .step-number {
  background-color: #337ab7;
  color: #fff;
}

.step-item .step-number {
  display: inline-block;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: #ddd;
  color: #666;
  text-align: center;
  line-height: 30px;
  font-size: 14px;
  font-weight: bold;
}

.step-item .step-title {
  display: block;
  margin-top: 5px;
  font-size: 14px;
  font-weight: bold;
}
.pb-container{
  display: flex;
  justify-content: ce;
}

.progress {
    width: 300px; /* adjust the width as needed */
     /* add this to center the progress bar */
}

.half-rating {
  width: 50px;
  height: 50px;
  background: "{{ asset('../assets/images/rating.png') }}" no-repeat center center;
  background-size: contain;
  position: relative;
}
.half-rating::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0.5) 50%);
    /* The left side will be fully transparent (100%), and the right will be 50% opacity */
}

#jenis-bahan-actions .btn {
    width: fit-content;
    height: 40px;
    margin: 0 10px;
    white-space: nowrap;
}
.dd {
  position: relative;
  display: inline-block;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: #4CAF50;
  color: #fff;
  cursor: pointer;
  transition: background-color 0.3s;
}

.dd-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  padding: 12px 16px;
  z-index: 1;
}

.dd-content ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dd-content li {
  margin-bottom: 10px;
}

.dd-content a {
  text-decoration: none;
  color: #333;
}

.dd:hover .dd-content {
        display: block;
      }

.dd.show .dd-content {
  display: block;
}
    </style>
    
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TA</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('../assets/img/favicon/favicon.ico')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- FontAwesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('../assets/vendor/fonts/boxicons.css')}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('../assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('../assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('../assets/css/demo.css')}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <link rel="stylesheet" href="{{ asset('../assets/vendor/libs/apex-charts/apex-charts.css')}}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Helpers -->
    <script src="{{ asset('../assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('../assets/js/config.js')}}"></script>

    

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('../assets/DataTables/datatables.css') }}">


    <!-- flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  </head>

  <body>
        <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include("layout.navbar")
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y" >
              <div class="text-end" style="margin-right: 25px;">
                <span id="current-time">
                    @php
                    echo now()->format("d-M-Y H:i:s");
                    @endphp
                </span>
              </div>
              <div class="card">
                    @yield('menu')
              </div>
            <!-- Content -->
            {{-- <div class="container-xxl flex-grow-1 container-p-y" >
                <div class="card" style="padding: 20px;">
                      @yield('content')
                </div>
            </div> --}}
              <!-- / Content -->
            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('../assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="{{ asset('../assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{ asset('../assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{ asset('../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{ asset('../assets/vendor/js/menu.js')}}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('../assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>

    <!-- Main JS -->
    <script src="{{ asset('../assets/js/main.js')}}"></script>

    <!-- Page JS -->
    <script src="{{ asset('../assets/js/dashboards-analytics.js')}}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/DataTables/datatables.js') }}"></script>
    <script>
        function updateCurrentTime() {
            var currentTimeElement = document.getElementById('current-time');

            var options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric',second: 'numeric'};
            var currentTime = new Date().toLocaleString('id-ID', options);
            currentTimeElement.innerText = currentTime;
        }

        setInterval(updateCurrentTime, 1000);
    </script>
    <script src="{{ asset('../assets/js/select2.js')}}"></script>
    @yield('script')
  </body>
</html>
