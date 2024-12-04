<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .login-container {
            display: flex;
            width: 100%; /* Make the container full width */
            height: 100%; /* Make the container full height */
            max-width: none; /* Remove max-width to allow full screen */
            box-shadow: none; /* Remove shadow for a cleaner look */
        }
        .login-image {
            flex: 70;
            background: url('{{ asset('assets/img/downloads/login.png') }}') no-repeat center center; /* Center the image */
            background-size: cover; 
            height: 100%; 
        }
        .login-form {
            flex: 30;
            padding: 3rem;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center the form vertically */
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-image"></div>
    <div class="login-form">
        <h2 class="text-center mb-4">PRINTAJA WEB</h2>
        <p class="text-center">Nice to see you again</p>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-outline-dark btn-block">Sign in with Google</button>
                <p class="mt-3"><a href="{{ route('register') }}">Don't have an account? Register here</a></p>
            </div>
        </form>
    </div>
</div>

</body>
</html>