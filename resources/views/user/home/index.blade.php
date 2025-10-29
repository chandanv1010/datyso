@extends('user.main')
{{-- @section('pageTitle', 'Trang chủ') --}}
@section('templateContent')
    @include('user.slider', ['sliders' => $sliders])
    <div class="container">
        <!-- Dòng sản phẩm chính -->
        <div class="my-5">
            <h1 class="ps-2 text-dark-blue fw-semibold fs-2 text-uppercase border-title mb-3">DÒNG SẢN PHẨM CHÍNH</h1>
            <div class="row mb-4">
                @foreach ($mainContent as $index => $main)
                    <div class="col-sm-4 mb-3 main-content">
                        <a href="{{ route('product.list', ['categorySlug' => $main->slug]) }}" class="">
                            <div class="h-100 position-relative border-0 shadow rounded-4 overflow-hidden">
                                <img src="{{ $main->image ?? asset('asset/images/Logo-shortcut.png') }}"
                                    class="w-100 h-100 border-0 rounded-4 main-content-image" alt="">
                                <div
                                    class="position-absolute top-50 start-50 translate-middle text-white text-center bg-dark w-100 h-100 border-0 rounded-4 opacity-50">
                                </div>
                                <div class="position-absolute top-50 start-50 translate-middle text-center w-75">
                                    <h2 class="text-decoration-none text-white fw-bolder">{{ $main->name }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mb-5">
            <h1 class="ps-2 text-dark-blue fw-semibold fs-2 text-uppercase border-title mb-5">Con số ấn tượng</h1>
            <div class="counter_wrapper text-center w-100">
                <div class="row">
                    <div class="col-md-3 col-sm-3 mb-5">
                        <div class="count_box st-box py-5 h-100">
                            <h1 class="text-danger fw-bolder fs-1"><span class="timer">300</span>+</h1>
                            <h4 class="fs-3 text-uppercase fw-bolder pt-4">Nhà cung cấp trên toàn cầu</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 mb-5">
                        <div class="count_box py-5 nd-box h-100 box_center">
                            <h1 class="text-danger fw-bolder fs-1"><span class="timer">10241</span>+</h1>
                            <h4 class="fs-3 text-uppercase fw-bolder pt-4">Sản phẩm & công nghệ tiên tiến</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 mb-5">
                        <div class="count_box py-5 rd-box h-100">
                            <h1 class="text-danger fw-bolder fs-1"><span class="timer">3</h1>
                            <h4 class="fs-3 text-uppercase fw-bolder pt-4">TRỤ SỞ CHÍNH </h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 mb-5">
                        <div class="count_box py-5 th-box h-100">
                            <h1 class="text-danger fw-bolder fs-1"><span class="timer">100</span>+</h1>
                            <h4 class="fs-3 text-uppercase fw-bolder pt-4">Nhà phân phối & đại lý tại 63 tỉnh
                                thành</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-5 bg-white py-5">
        <div class="container">
            <h1 class="ps-2 text-dark-blue fw-semibold fs-2 text-uppercase border-title mb-1">Khách hàng nói gì về
                chúng tôi</h1>
            <div class="mt-4">
                <div class="row mb-3">
                    <div class="col-4 d-flex align-items-center">
                                <img src="{{ asset('asset/images/LGJT.jpg') }}" class="w-100" alt="">
                    </div>
                    <div class="col-8 d-flex align-items-center text-justify">
                        <span>
                        “Chúng tôi, CÔNG TY TNHH JT TUBE VIỆT NAM, rất vinh dự khi được hợp tác với Công ty Cổ phần Datyso Việt Nam. Qua quá trình làm việc chung, chúng tôi đã nhận thấy Datyso là một đối tác đáng tin cậy với đội ngũ nhân viên chuyên nghiệp, trách nhiệm cao. Chất lượng dịch vụ và sản phẩm của Datyso luôn đảm bảo, đáp ứng tốt mọi yêu cầu của chúng tôi. Datyso không ngừng đổi mới và sáng tạo, mang đến những giải pháp kinh doanh hiệu quả và độc đáo. Sự minh bạch và trung thực trong giao dịch của Datyso đã tạo nên sự tin tưởng vững chắc giữa hai bên. Chúng tôi tin tưởng rằng, với sự hợp tác chiến lược này, cả hai công ty sẽ cùng phát triển và đạt được nhiều thành công trong tương lai.” 
                        
                            <div class="text-danger text-end">
                                <p class="mb-0 fw-bolder">Đại diện khách hàng</p>
                                <span class="fst-italic">CÔNG TY TNHH JT TUBE VIỆT NAM </span>
                            </div>
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-8 d-flex align-items-center text-justify">
                        <span>
                            “Chúng tôi, CÔNG TY CỔ PHẦN THỐNG NHẤT HÀ NỘI, rất hài lòng khi hợp tác với Công ty Cổ phần Datyso Việt Nam để mua Máy cắt ống laser GS90 3000W tự động cấp phôi. Máy có hiệu suất vượt trội và độ chính xác cao, giúp chúng tôi nâng cao hiệu quả sản xuất. Đội ngũ kỹ thuật của Datyso đã hỗ trợ tận tâm trong quá trình cài đặt và sử dụng. Sự chuyên nghiệp và trách nhiệm của Datyso đã tạo nên một môi trường hợp tác tin cậy và hiệu quả. Chúng tôi mong muốn tiếp tục đồng hành cùng Datyso trong các dự án tương lai.” 

                            <div class="text-danger">
                                <p class="mb-0 fw-bolder">Đại diện khách hàng</p>
                                <span class="fst-italic">Giám Đốc CÔNG TY CỔ PHẦN THỐNG NHẤT</span>
                            </div>
                        </span>
                    </div>
                    <div class="col-4 d-flex align-items-center">
                        <img src="{{ asset('asset/images/thongnhat.jpg') }}" class="w-100" alt="">
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 d-flex align-items-center">
                        <img src="{{ asset('asset/images/chungson.jpg') }}" class="w-100" alt="">
                    </div>
                    <div class="col-8 d-flex align-items-center text-justify">
                        <span>
                            “Chúng tôi, Công ty TNHH cơ khí Chung Sơn, rất hài lòng khi hợp tác với Công ty Cổ phần Datyso Việt Nam để mua Máy cắt laser GF2060JH 20KW Raycus. Máy có hiệu suất mạnh mẽ, độ chính xác cao, giúp nâng cao chất lượng và hiệu quả sản xuất. Đội ngũ kỹ thuật của Datyso luôn hỗ trợ tận tình, đảm bảo máy hoạt động hiệu quả. Sự chuyên nghiệp của Datyso từ tư vấn đến hậu mãi đã tạo nên sự tin cậy và hài lòng. Chúng tôi tin rằng sự hợp tác với Datyso sẽ mở ra nhiều cơ hội phát triển và nâng cao năng lực sản xuất trong tương lai. Chúng tôi mong muốn tiếp tục đồng hành cùng Datyso trong các dự án sắp tới.”  

                            <div class="text-danger text-end">
                                <p class="mb-0 fw-bolder">Đại diện khách hàng</p>
                                <span class="fst-italic">Công ty TNHH cơ khí Chung Sơn</span>
                            </div>
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-8 d-flex align-items-center text-justify">
                        <span>
                            Giám đốc điều hành T&T PLUS VINA, chia sẻ: "Chúng tôi, Công ty TNHH T & T PLUS VINA, rất hài lòng với việc hợp tác với Công ty Cổ phần Datyso Việt Nam trong việc mua Máy chấn trung tâm S2000. Chúng tôi tin tưởng AT-S2000 là giải pháp hoàn hảo cho nhu cầu của T&T PLUS VINA. Máy móc hiện đại cùng dịch vụ chuyên nghiệp từ Datyso sẽ góp phần thúc đẩy hiệu quả hoạt động và đưa T&T PLUS VINA lên tầm cao mới. Đội ngũ hỗ trợ của Datyso rất tận tâm và chuyên nghiệp, giúp chúng tôi làm quen nhanh chóng với thiết bị. Chúng tôi mong muốn tiếp tục hợp tác lâu dài với Datyso trong tương lai.” 
                            <div class="text-danger">
                                <p class="mb-0 fw-bolder">Đại diện khách hàng</p>
                                <span class="fst-italic">Công ty TNHH T & T PLUS VINA</span>
                            </div>
                        </span>
                    </div>
                    <div class="col-4 d-flex align-items-center">
                        <img src="{{ asset('asset/images/tt.png') }}" class="w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        {{-- Tin tức --}}
        <div class="mb-5">
            <h1 class="ps-2 text-dark-blue fw-semibold fs-2 text-uppercase border-title mb-3">TIN TỨC</h1>
            <div class="row">
                @foreach ($posts as $post)
                    <div class="col-sm-4 mb-2">
                        <div class="card border-0 rounded-4 bg-transparent h-100">
                            <img src="{{ $post->image }}" class="card-img-top rounded-4 w-100 shadow"
                                alt="{{ $post->name }}" style="max-height: 12em">
                            <div class="card-body py-2 px-0 d-flex flex-column">
                                <small class="fw-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em"
                                        fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                    </svg>
                                    {{ date_format($post->updated_at, 'd/m/Y') }}
                                </small>
                                <p class="mb-0 fw-semibold mb-1"><a href="{{ route('post.detail', ['postSlug' => $post->slug]) }}"
                                        class="text-decoration-none text-black">{{ $post->name }}</a></p>
                                <a href="{{ route('post.detail', ['postSlug' => $post->slug]) }}"
                                    class="mt-auto text-decoration-none fw-medium fst-italic text-dark-blue"><small>Xem chi
                                        tiết</small></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Địa chỉ -->
        {{-- @dd($address) --}}
        @if(!is_null($address) && $address->count() > 0)
        <div class="mb-5">
            <h1 class="ps-2 text-dark-blue fw-semibold fs-2 text-uppercase border-title mb-3">BẢN ĐỒ GOOGLE MAPS </h1>
            <div class="row">
                @foreach($address as $item)
                <div class="col-sm-6">
                    <div class="map-item">
                        <div class="office-name">{{ $item->name }}</div>
                        <p>- <strong>Địa chỉ</strong>: {{ $item->address }}</p>
                        <p>- <strong>Điện thoại</strong>: {{ $item->phone }}</p>
                        <p>- <strong>Hotline</strong>: {{ $item->hotline }}</p>
                        <p>- <strong>Email</strong>: {{ $item->email }}</p>

                        <div class="map-container">
                            {!! $item->map !!}
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>
        </div>
        @endif
    </div>
@endsection
@section('footer')
    @include('user.buttonSocial')

    <script>
         document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.find-location-btn');
        const iframe = document.getElementById('map-iframe');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const mapSrc = this.getAttribute('data-map');
                if (mapSrc) {
                    iframe.src = mapSrc;
                }
            });
        });
    });
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            spaceBetween: 25,
            pagination: {
                el: ".swiper-pagination",
                type: "fraction",
            },
        });

        var mySwiperNews = new Swiper('.mySwiperNews', {
            loop: true,
            spaceBetween: 25,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

        $(document).ready(function() {
            $('.text-news').each(function() {
                var maxLength = 180;
                var text = $(this).text();
                if (text.length > maxLength) {
                    var truncatedText = text.substring(0, maxLength) + '...';
                    $(this).text(truncatedText);
                }
            });
        });

        const animationDuration = 3000;

        const frameDuration = 1000 / 60;

        const totalFrames = Math.round(animationDuration / frameDuration);

        const easeOutQuad = t => t * (2 - t);

        const animateCountUp = el => {
            let frame = 0;
            const countTo = parseInt(el.innerHTML, 10);

            const counter = setInterval(() => {
                frame++;

                const progress = easeOutQuad(frame / totalFrames);

                const currentCount = Math.round(countTo * progress);


                if (parseInt(el.innerHTML, 10) !== currentCount) {
                    el.innerHTML = currentCount;
                }

                if (frame === totalFrames) {
                    clearInterval(counter);
                }
            }, frameDuration);
        };

        const countupEls = document.querySelectorAll('.timer');
        countupEls.forEach(animateCountUp);
    </script>

    <script src="{{ asset('asset/js/home.js') }}"></script>
@endsection
