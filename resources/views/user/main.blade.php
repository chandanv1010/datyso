<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TMBWLK3W');</script>
<!-- End Google Tag Manager -->
    {{-- <title>@yield('pageTitle')</title> --}}
    <title>{{ $seo['meta_title'] ?? '' }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ url('asset/images/Logo-shortcut.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/imagehover.css/2.0.0/css/imagehover.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- SEO -->
    <!-- GOOGLE -->
    
    <meta name="description"  content="{{ $seo['meta_description'] ?? '' }}" />
    <meta name="keyword"  content="{{ $seo['meta_keyword'] ?? '' }}" />
    <link rel="canonical" href="{{ $seo['canonical'] ?? '' }}" />		
    <meta property="og:locale" content="vi_VN" />
    <!-- for Facebook -->
    <meta property="og:title" content="{{ $seo['meta_title'] ?? '' }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ $seo['meta_image'] ?? '' }}" />
    <meta property="og:url" content="{{ $seo['canonical'] ?? '' }}" />		
    <meta property="og:description" content="{{ $seo['meta_description'] ?? '' }}" />
    <meta property="og:site_name" content="" />
    <meta property="fb:admins" content=""/>
    <meta property="fb:app_id" content="" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="{{ $seo['meta_title'] ?? '' }}" />
    <meta name="twitter:description" content="{{ $seo['meta_description'] ?? '' }}" />
    <meta name="twitter:image" content="{{ $seo['meta_image'] ?? '' }}" /> 


    <!-- ************************************************************** -->
    <link rel="stylesheet" href="{{ asset('/asset/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('/asset/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/fix.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
</head>



<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TMBWLK3W"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Navbar -->
    @yield('layout-content')
    @include('user.layouts.header')
    @yield('templateContent')

    @include('user.layouts.footer')

    @include('user.buttonSocial')


    {!! isset($schema) ? $schema : null !!}

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    <!-- Jquery CDN -->
    

    <!-- Slicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('asset/js/home.js') }}"></script>
    <script src="{{ asset('asset/js/product-page.js') }}"></script>


   
    @yield('footer')
    <script src="{{ asset('asset/toastr.min.js') }}"></script>
    <script src="{{ asset('asset/js/fix.js') }}"></script>
</body>

</html>
