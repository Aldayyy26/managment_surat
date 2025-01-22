<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>SPETI</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
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
<style>
  .feature-number {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    background-color: #f1f1f1;
    border-radius: 50%;
}

</style>

<body class="index-page">

<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="header-container container-fluid container-xl d-flex justify-content-between align-items-center">
    <a href="{{ url('/') }}" class="logo d-flex align-items-center">
      <h1 class="sitename">SPETI</h1>
    </a>

    <!-- Navigation Menu -->
    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="#hero" class="active">Home</a></li>
        <li class="dropdown">
          <a href="{{ route('users.surat.create') }}"><span>Layanan Surat</span></a>
        </li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <!-- User Dropdown -->
    @auth
    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" class="text-gray-700 font-medium">
        {{ Auth::user()->name }} <i class="bi bi-chevron-down"></i>
      </button>
      <div
        x-show="open" 
        @click.away="open = false" 
        class="absolute right-0 mt-2 bg-white border rounded shadow-lg w-40"
        style="display: none;"
      >
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700">Dashboard</a>
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <a 
            href="{{ route('logout') }}" 
            onclick="event.preventDefault(); this.closest('form').submit();" 
            class="block px-4 py-2 text-sm text-gray-700"
          >
            Log Out
          </a>
        </form>
      </div>
    </div>
    @else
    <a class="btn-getstarted" href="{{ url('login') }}">Login</a>
    @endauth
  </div>
</header>


  <main class="main">
    <!-- Hero Section -->
    <section id="hero" class="hero section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
              <div class="company-badge mb-4">
                <i class="bi bi-gear-fill me-2"></i>
                Membuat surat untuk kebutuhanmu
              </div>

              <h1 class="mb-4">
                Teknik Informatika <br>
                Politeknik Harapan Bersama <br>
                <span class="accent-text">SPETI</span>
              </h1>

              <p class="mb-4 mb-md-5">
                Membuat surat sekarang lebih mudah di website.
                Untuk menghindari antrian panjang dan mempercepat waktu anda.
              </p>

              <div class="hero-buttons">
                <a href="#about" class="btn btn-primary me-0 me-sm-2 mx-1">Mulai Sekarang</a>
                <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="btn btn-link mt-2 mt-sm-0 glightbox">
                  <i class="bi bi-play-circle me-1"></i>
                  Video Tutorial
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
              <img src="{{ asset('/img/illustration-1.webp') }}" alt="Hero Image" class="img-fluid">
              <!-- <div class="customers-badge">
                <div class="customer-avatars">
                  <img src="{{ asset('/img/avatar-1.webp') }}" alt="Customer 1" class="avatar">
                  <img src="{{ asset('/img/avatar-2.webp') }}" alt="Customer 2" class="avatar">
                  <img src="{{ asset('/img/avatar-3.webp') }}" alt="Customer 3" class="avatar">
                  <img src="{{ asset('/img/avatar-4.webp') }}" alt="Customer 4" class="avatar">
                  <img src="{{ asset('/img/avatar-5.webp') }}" alt="Customer 5" class="avatar">
                  <span class="avatar more">12+</span>
                </div>
                <p class="mb-0 mt-2">12,000+ lorem ipsum dolor sit amet consectetur adipiscing elit</p>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Hero Section -->

    <section id="features-2" class="features-2 section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">

        <div class="col-lg-4">

<div class="feature-item text-end mb-5" data-aos="fade-right" data-aos-delay="200">
  <div class="d-flex align-items-center justify-content-end gap-4">
    <div class="feature-content">
      <h3>Dipakai di Berbagai Device</h3>
      <p>Aplikasi ini dirancang agar fleksibel dan mudah diakses dari berbagai jenis perangkat. Tak peduli apakah Anda menggunakan ponsel, tablet, atau komputer, pengalaman pengguna tetap optimal.</p>
    </div>
    <div class="feature-icon flex-shrink-0">
      <span class="feature-number">1</span>
    </div>
  </div>
</div><!-- End .feature-item -->

<div class="feature-item text-end mb-5" data-aos="fade-right" data-aos-delay="300">
  <div class="d-flex align-items-center justify-content-end gap-4">
    <div class="feature-content">
      <h3>Bebas Antrian</h3>
      <p>Aplikasi ini dirancang untuk mempermudah user untuk membuat surat.</p>
    </div>
    <div class="feature-icon flex-shrink-0">
      <span class="feature-number">2</span>
    </div>
  </div>
</div><!-- End .feature-item -->

<div class="feature-item text-end" data-aos="fade-right" data-aos-delay="400">
  <div class="d-flex align-items-center justify-content-end gap-4">
    <div class="feature-content">
      <h3>Memilih surat</h3>
      <p>Aplikasi ini menyediakan semua layanan surat program studi.</p>
    </div>
    <div class="feature-icon flex-shrink-0">
      <span class="feature-number">3</span>
    </div>
  </div>
</div><!-- End .feature-item -->

</div>


          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="phone-mockup text-center">
              <img src="{{ asset('/img/phb.png') }}" alt="Phone Mockup" class="img-fluid">
            </div>
          </div><!-- End Phone Mockup -->

          <div class="col-lg-4">

    <div class="feature-item mb-5" data-aos="fade-left" data-aos-delay="200">
      <div class="d-flex align-items-center gap-4">
        <div class="feature-icon flex-shrink-0">
          <span class="feature-number">4</span>
        </div>
        <div class="feature-content">
          <h3>Bisa dibuat dimana saja</h3>
          <p>Memastikan user dapat mengakses dimana saja dan kapan saja.</p>
        </div>
      </div>
    </div><!-- End .feature-item -->

    <div class="feature-item mb-5" data-aos="fade-left" data-aos-delay="300">
      <div class="d-flex align-items-center gap-4">
        <div class="feature-icon flex-shrink-0">
          <span class="feature-number">5</span>
        </div>
        <div class="feature-content">
          <h3>Unduh surat</h3>
          <p>Mempermudah user untuk mengunduh dan dapat langsung di sambungkan ke alat print untuk di cetak.</p>
        </div>
      </div>
    </div><!-- End .feature-item -->

    <div class="feature-item" data-aos="fade-left" data-aos-delay="400">
      <div class="d-flex align-items-center gap-4">
        <div class="feature-icon flex-shrink-0">
          <span class="feature-number">6</span>
        </div>
        <div class="feature-content">
          <h3>Layanan Cepat</h3>
          <p>Optimalisasi aplikasi untuk kompatibilitas di berbagai browser guna memastikan kenyamanan pengguna tanpa gangguan.</p>
        </div>
      </div>
    </div><!-- End .feature-item -->

</div>

        </div>

      </div>

    </section><!-- /Features 2 Section -->

    <!-- Services Section -->
  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>&copy; <strong class="px-1 sitename">Aldi Bhanu Azhar</strong> All Rights Reserved</p>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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
