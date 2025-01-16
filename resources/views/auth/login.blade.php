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
                        <p>Akses ke seluruh layanan akademik kampus dengan mudah dan cepat.</p>
                        <!-- <div class="page-links">
                            <a href="{{ url('login') }}" class="active">Login</a><a href="{{ url('register') }}">Register</a>
                        </div> -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <input class="form-control" type="email" name="email" placeholder="Username" required>
                            <input class="form-control" type="password" name="password" placeholder="Password" required>
                            <div class="form-button">
                                <button id="submit" type="submit" class="ibtn">Login</button> <a href="#">Lupa password?</a>
                            </div>
                        </form>
                        <!-- <div class="other-links">
                            <span>Or login with</span><a href="#">Facebook</a><a href="#">Google</a><a href="#">LinkedIn</a>
                        </div> -->
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
