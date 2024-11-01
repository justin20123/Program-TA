@extends('layout.sneat')
@section('breadcrumb')
    @yield('breadcrumb')
@endsection

@section('menu')
@yield('title')
@yield('modal')
@yield('buttontambah')
<table class="table">
    <thead>
        <tr>
            <th>Opsi</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @yield("tableitem")
    </tbody>
</table>
@endsection
