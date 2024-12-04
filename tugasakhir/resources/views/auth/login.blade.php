@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%;">
        <div class="text-center mb-4">
            <h4>PRINTAJA WEB</h4>
            <p class="text-muted">Nice to see you again</p>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </form>

        <div class="text-center">
            <p>Don't have an account? <a href="{{ route('register') }}">Sign up now</a></p>
        </div>
    </div>
</div>
@endsection
