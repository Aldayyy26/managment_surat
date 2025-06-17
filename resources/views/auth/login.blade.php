<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPETI</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/csslogin/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/csslogin/fontawesome-all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/csslogin/iofrm-style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/csslogin/iofrm-theme4.css') }}">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/favicon/android-chrome-512x512.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">

</head>

<body>
    <div class="form-body">
        <div class="website-logo">
            <a href="{{ url('/') }}">
                <div class="logo">
                    <img class="logo-size" src="{{ asset('img/logo-light.svg') }}" alt="">
                </div>
            </a>
        </div>
        <div class="iofrm-layout">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder">
                    <img src="{{ asset('img/graphic1.svg') }}" alt="">
                </div>
            </div>
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items">
                        <h3>Login untuk membuat surat.</h3>
                        <p>Akses pembuatan surat dengan cepat dan mudah tanpa antrian.</p>
                        @if ($errors->has('email') || $errors->has('password'))
                        <div class="alert alert-danger">
                            <strong>Gagal login!</strong> Email atau password yang Anda masukkan salah.
                        </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <input class="form-control" type="email" name="email" placeholder="Email" required>
                            <input class="form-control" type="password" name="password" placeholder="Password" required>
                            <div class="form-button">
                                <button id="submit" type="submit" class="ibtn">Login</button> <a href="#">Lupa password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/jslogin/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jslogin/popper.min.js') }}"></script>
    <script src="{{ asset('js/jslogin/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jslogin/main.js') }}"></script>
</body>

</html>