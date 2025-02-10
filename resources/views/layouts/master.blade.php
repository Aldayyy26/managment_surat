<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPETI</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;400;500;700;900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  
  <!-- Main CSS File -->
  <link href="{{ asset('/css/main.css') }}" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="index-page">
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container-fluid container-xl d-flex justify-content-between align-items-center">
      <a href="{{ url('/') }}" class="logo d-flex align-items-center">
        <h1 class="sitename">SPETI</h1>
      </a>
      
      <!-- User Dropdown -->
      @auth
      <div x-data="{ open: false }" class="position-relative">
        <button @click="open = !open" class="btn btn-light">
          {{ Auth::user()->name }} <i class="bi bi-chevron-down"></i>
        </button>
        <div x-show="open" @click.away="open = false" class="dropdown-menu dropdown-menu-end shadow-lg mt-2">
          <a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a>
          <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">Log Out</button>
          </form>
        </div>
      </div>
      @else
      <a class="btn btn-primary" href="{{ url('login') }}">Login</a>
      @endauth
    </div>
  </header>

  <main class="container py-5">
    @yield('content')
  </main>

  <footer id="footer" class="footer text-center py-4 bg-light">
    <div class="container">
      <p>&copy; <strong class="sitename">Aldi Bhanu Azhar</strong> All Rights Reserved</p>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('/vendor/purecounter/purecounter_vanilla.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('/js/main.js') }}"></script>
</body>

</html>
