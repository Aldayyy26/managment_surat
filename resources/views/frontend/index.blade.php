<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>SPETI</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/favicon/android-chrome-192x192.png') }}">
  <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/favicon/android-chrome-512x512.png') }}">
  <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">


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

  [x-cloak] {
    display: none !important;
  }
</style>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl d-flex justify-content-between align-items-center">
      <a href="{{ url('/') }}" class="logo d-flex align-items-center">
        <h1 class="sitename fs-4 fs-xl-1">SPETI</h1>
      </a>

      <!-- Navigation Menu -->
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="{{ url('/pengajuan_surat/create') }}">Layanan Surat</a></li>

          @guest
          <li class="d-block d-xl-none">
            <a href="{{ url('login') }}">Login</a>
          </li>
          @endguest

          @auth
          <!-- Dropdown user (MOBILE) -->
          <li x-data="{ openMobile: false }" class="d-block d-xl-none position-relative">
            <button @click="openMobile = !openMobile" class="btn w-100 text-start d-flex justify-content-between align-items-center">
              <span>{{ Auth::user()->name }}</span>
              <i class="bi bi-chevron-down ms-1"></i>
            </button>

            <div x-show="openMobile" x-transition x-cloak
              class="bg-white border rounded shadow mt-2 p-2 position-absolute w-100"
              style="z-index: 1000;">
              <a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a>
              <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}"
                  onclick="event.preventDefault(); this.closest('form').submit();"
                  class="dropdown-item">Log Out</a>
              </form>
            </div>
          </li>
          @endauth
        </ul>

        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <!-- Dropdown user (DESKTOP) -->
      @auth
      <div x-data="{ openDesktop: false }" class="position-relative d-none d-xl-block">
        <button @click="openDesktop = !openDesktop" class="btn btn-link text-decoration-none text-dark">
          {{ Auth::user()->name }} <i class="bi bi-chevron-down"></i>
        </button>
        <div x-show="openDesktop" x-transition @click.away="openDesktop = false"
          class="position-absolute bg-white border rounded shadow p-2 mt-2"
          style="right: 0; min-width: 150px; z-index: 1000;" x-cloak>
          <a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a>
          <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
              onclick="event.preventDefault(); this.closest('form').submit();"
              class="dropdown-item">Log Out</a>
          </form>
        </div>
      </div>
      @else
      <div class="d-none d-xl-block">
        <a class="btn-getstarted" href="{{ url('login') }}">Login</a>
      </div>
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

              <!-- <div class="hero-buttons">
                <a href="#about" class="btn btn-primary me-0 me-sm-2 mx-1">Mulai Sekarang</a>
                <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="btn btn-link mt-2 mt-sm-0 glightbox">
                  <i class="bi bi-play-circle me-1"></i>
                  Video Tutorial
                </a>
              </div> -->
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
      <p>&copy; <strong class="px-1 sitename">Teknik Informatika </strong> All Rights Reserved</p>
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