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
    <link rel="stylesheet" href="{{ asset('asset/css/fix.css') }}?time={{ time() }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <style>
        #google_translate_element{
            position: fixed;
            top:0;
            right:0;
        }
        #google_translate_element select {
            background: #0088ff;
            color: white;
            border: none;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
        }

    </style>
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


    <!-- GOOGLE TRANSLATE -->
    <div id="google_translate_element"></div>

    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
        pageLanguage: 'vi', // Ngôn ngữ gốc của web (vi = tiếng Việt)
        includedLanguages: 'vi,en,fr,ja,zh-CN,ko,de,ru,es', // Các ngôn ngữ muốn hỗ trợ
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
        }, 'google_translate_element');
    }
    </script>

    <script type="text/javascript" 
    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>

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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tocOriginal = document.getElementById('toc-original');
            const tocToggle = document.getElementById('toc-toggle');
            const tocSidebar = document.getElementById('toc-sidebar');
            const tocClose = document.getElementById('toc-close');
            const content = document.getElementById('content');

            if (!tocOriginal || !tocToggle || !content) return;

            tocToggle.style.display = 'none';

            function positionTocButton() {
                const rect = content.getBoundingClientRect();
                const rightSpace = window.innerWidth - rect.right;
                tocToggle.style.right = `${rightSpace - 80}px`; // cách content 20px
            }

            function checkTocPosition() {
                const rect = tocOriginal.getBoundingClientRect();
                if (rect.bottom < 0) {
                    tocToggle.style.display = 'block';
                } else {
                    tocToggle.style.display = 'none';
                }
            }

            // Gọi khi load & resize & scroll
            positionTocButton();
            checkTocPosition();

            window.addEventListener('resize', positionTocButton);
            window.addEventListener('scroll', () => {
                checkTocPosition();
                positionTocButton();
            });

            // ✅ Kiểm tra tồn tại rồi mới add event
            tocToggle.addEventListener('click', () => tocSidebar.classList.toggle('open'));

            if (tocClose) {
                tocClose.addEventListener('click', () => tocSidebar.classList.remove('open'));
            }

            document.addEventListener('click', (e) => {
                if (!tocSidebar.contains(e.target) && !tocToggle.contains(e.target)) {
                    tocSidebar.classList.remove('open');
                }
            });
        });
    </script>
</body>

</html>
