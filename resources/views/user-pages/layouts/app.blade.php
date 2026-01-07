<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>YTBOOK</title>
    <meta name="description" content="YTBOOK is India's leading online education platform connecting students and teachers. Join live classes, practice mock tests, and access comprehensive study materials for JEE, NEET, and competitive exams.">
    <meta name="keywords"
        content="online education, live classes, mock tests, JEE preparation, NEET coaching, online tutoring, study platform India">
    <meta name="author" content="Givni Private Limited">
    <link rel="shortcut icon" href="{{ asset('assets/user/img/logo/favicon.png') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/user/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/style.css') }}">
</head>

<body class="home-6">
    {{-- <div id="preloader">
        <div class="preloader-wrap ">
            <div class="loader">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </div> --}}
    <div class="ed-up">
        <a href="#" class="ed-scrollup text-center"><i class="fas fa-chevron-up"></i></a>
    </div>

    @include('user-pages.layouts._header')

    @yield('content')

    @include('user-pages.layouts._footer')

    <!-- For Js Library -->
    <script src="{{ asset('assets/user/js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/appear.js') }}"></script>
    <script src="{{ asset('assets/user/js/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/knob.js') }}"></script>
    <script src="{{ asset('assets/user/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/jqueryui.js') }}"></script>
    <script src="{{ asset('assets/user/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/jquery.marquee.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/lenis.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/split-type.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/SplitText.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/script.js') }}"></script>
</body>

</html>