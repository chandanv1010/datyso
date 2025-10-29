 <!-- Footer -->
 <footer class="footer text-lg-start  text-muted bg-main">
    <div class="container text-center text-sm-start text-white">
        <div class="row pt-5">
            <div class="col-sm-4">
                <div class="mb-4">
                    <a href="#">
                        <img src="{{asset('/asset/images/logotrang.jpg')}}" alt="" width="190">
                    </a>
                </div>
                <div class="mb-2 d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem" fill="currentColor"
                            class="mt-n1 text-white bi bi-geo-alt" viewBox="0 0 16 16" style="color: #002ab0;">
                            <path
                                d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>
                    </div>
                    <div class="mx-2"></div>
                    <div>
                        <span>Miền Bắc: Số 345 Phố Thú Y, Đức Thượng, Hoài Đức, Hà Nội</span>
                        <br>
                        <span>Miền Nam: 1054b Lê Văn Khương, P, Quận 12, TP.HCM</span>
                    </div>
                </div>
                <div class="mb-2 d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem" fill="currentColor"
                            class="mt-n1 text-white bi bi-envelope" viewBox="0 0 16 16" style="color: #002ab0;">
                            <path
                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                        </svg>
                    </div>
                    <div class="mx-2"></div>
                    <div>
                        <span>Datysojsc@gmail.com</span>
                    </div>
                </div>
                <div class="mb-2 d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem" fill="currentColor"
                            class="mt-n1 text-white bi bi-telephone" viewBox="0 0 16 16" style="color: #002ab0;">
                            <path
                                d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                        </svg>
                    </div>
                    <div class="mx-2"></div>
                    <div>
                        <span>0963.535.196</span>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="#">
                        <img src="{{ asset('asset/images/bo-cong-thuong.png') }}" alt="" width="190">
                    </a>
                </div>
            </div>
            <div class="col-sm-2 hand">
                <h6 class="text-white text-uppercase fw-semibold mb-4">
                    Thông tin chung
                </h6>
                <div class="text-capitalize">
                    <a href=" {{ route('introduce.index') }} " class="d-block nav-link mb-2">Giới thiệu</a>
                    <a href=" {{ route('post.index') }} " class="d-block nav-link mb-2">Tin tức</a>
                    <a href=" {{ route('solution.index') }} " class="d-block nav-link mb-2">Giải pháp</a>
                    <a href=" {{ route('contact.index') }} " class="d-block nav-link mb-2">Liên hệ</a>
                </div>
            </div>
            <div class="col-sm-3 hand">
                <h6 class="text-white text-uppercase fw-semibold mb-4">
                    Chính sách
                </h6>
                <a href="{{ url('chinh-sach/chinh-sach-thanh-toan/chi-tiet') }}" class="d-block nav-link mb-2">Chính sách thanh toán </a>
                <a href="{{ url('chinh-sach/chinh-sach-van-chuyen/chi-tiet') }}" class="d-block nav-link mb-2">Chính sách vận chuyển</a>
                <a href="{{ url('chinh-sach/chinh-sach-bao-hanh/chi-tiet') }}" class="d-block nav-link mb-2">Chính sách bảo hành</a>
                <a href="{{ url('chinh-sach/chinh-sach-kiem-hang/chi-tiet') }}" class="d-block nav-link mb-2">Chính sách kiểm hàng</a>
                <a href="{{ url('chinh-sach/chinh-sach-khieu-nai/chi-tiet') }}" class="d-block nav-link mb-2">Chính sách xử lý khiếu nại</a>
                <a href="{{ url('chinh-sach/chinh-sach-bao-mat/chi-tiet') }}" class="d-block nav-link mb-2">Chính sách bảo mật</a>
            </div>

            <div class="col-sm-3 hand">
                <h6 class="text-white text-uppercase fw-semibold mb-4">Hỗ trợ</h6>
                <span class="d-block mb-2">Nhập email của bạn và chúng tôi sẽ gửi cho bạn thêm thông tin về các
                    chương trình khuyến mãi mới và các sản phẩm mới mới nhất.</span>
                <form class="d-flex" role="search">
                    <input class="form-control me-1" type="search" placeholder="Nhập Email" aria-label="Search">
                    <button type="button" class="btn btn-primary">Gửi</button>
                </form>
                <div class="mt-2">
                    <a href="#" style="text-decoration: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="3em" height="2.9em"
                            viewBox="0 0 48 48">
                            <path fill="#2962ff"
                                d="M15,36V6.827l-1.211-0.811C8.64,8.083,5,13.112,5,19v10c0,7.732,6.268,14,14,14h10	c4.722,0,8.883-2.348,11.417-5.931V36H15z">
                            </path>
                            <path fill="#eee"
                                d="M29,5H19c-1.845,0-3.601,0.366-5.214,1.014C10.453,9.25,8,14.528,8,19	c0,6.771,0.936,10.735,3.712,14.607c0.216,0.301,0.357,0.653,0.376,1.022c0.043,0.835-0.129,2.365-1.634,3.742	c-0.162,0.148-0.059,0.419,0.16,0.428c0.942,0.041,2.843-0.014,4.797-0.877c0.557-0.246,1.191-0.203,1.729,0.083	C20.453,39.764,24.333,40,28,40c4.676,0,9.339-1.04,12.417-2.916C42.038,34.799,43,32.014,43,29V19C43,11.268,36.732,5,29,5z">
                            </path>
                            <path fill="#2962ff"
                                d="M36.75,27C34.683,27,33,25.317,33,23.25s1.683-3.75,3.75-3.75s3.75,1.683,3.75,3.75	S38.817,27,36.75,27z M36.75,21c-1.24,0-2.25,1.01-2.25,2.25s1.01,2.25,2.25,2.25S39,24.49,39,23.25S37.99,21,36.75,21z">
                            </path>
                            <path fill="#2962ff" d="M31.5,27h-1c-0.276,0-0.5-0.224-0.5-0.5V18h1.5V27z"></path>
                            <path fill="#2962ff"
                                d="M27,19.75v0.519c-0.629-0.476-1.403-0.769-2.25-0.769c-2.067,0-3.75,1.683-3.75,3.75	S22.683,27,24.75,27c0.847,0,1.621-0.293,2.25-0.769V26.5c0,0.276,0.224,0.5,0.5,0.5h1v-7.25H27z M24.75,25.5	c-1.24,0-2.25-1.01-2.25-2.25S23.51,21,24.75,21S27,22.01,27,23.25S25.99,25.5,24.75,25.5z">
                            </path>
                            <path fill="#2962ff"
                                d="M21.25,18h-8v1.5h5.321L13,26h0.026c-0.163,0.211-0.276,0.463-0.276,0.75V27h7.5	c0.276,0,0.5-0.224,0.5-0.5v-1h-5.321L21,19h-0.026c0.163-0.211,0.276-0.463,0.276-0.75V18z">
                            </path>
                        </svg>
                    </a>
                    <a href="#" style="text-decoration: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="3em" height="3em"
                            viewBox="0 0 48 48">
                            <path fill="#039be5" d="M24 5A19 19 0 1 0 24 43A19 19 0 1 0 24 5Z"></path>
                            <path fill="#fff"
                                d="M26.572,29.036h4.917l0.772-4.995h-5.69v-2.73c0-2.075,0.678-3.915,2.619-3.915h3.119v-4.359c-0.548-0.074-1.707-0.236-3.897-0.236c-4.573,0-7.254,2.415-7.254,7.917v3.323h-4.701v4.995h4.701v13.729C22.089,42.905,23.032,43,24,43c0.875,0,1.729-0.08,2.572-0.194V29.036z">
                            </path>
                        </svg>
                    </a>
                    <a href="#" style="text-decoration: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="3.3em" height="3.3em"
                            viewBox="0 0 48 48">
                            <path fill="#FF3D00"
                                d="M43.2,33.9c-0.4,2.1-2.1,3.7-4.2,4c-3.3,0.5-8.8,1.1-15,1.1c-6.1,0-11.6-0.6-15-1.1c-2.1-0.3-3.8-1.9-4.2-4C4.4,31.6,4,28.2,4,24c0-4.2,0.4-7.6,0.8-9.9c0.4-2.1,2.1-3.7,4.2-4C12.3,9.6,17.8,9,24,9c6.2,0,11.6,0.6,15,1.1c2.1,0.3,3.8,1.9,4.2,4c0.4,2.3,0.9,5.7,0.9,9.9C44,28.2,43.6,31.6,43.2,33.9z">
                            </path>
                            <path fill="#FFF" d="M20 31L20 17 32 24z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <hr class="text-white">

    <div class="mt-4 text-white">
        <span class="d-block text-center fs-7">&copy; Copyright 2024 - Công Ty Cổ Phần Datyso Việt
            Nam</span>
        <span class="d-block text-center fs-7">Bản quyền thuộc về Công ty Cổ phần Datyso Việt Nam
            Giấy CNDKKD: 0106684335 được cấp bởi Sở Kế hoạch và Đầu tư Hà Nội</span>
    </div>
    <!-- <button class="border-0 rounded-circle text-primary" onclick='window.scrollTo({top: 0, behavior: "smooth"});'
        style="position: fixed; bottom: 4rem; right: 1rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="2rem" height="2rem" fill="currentColor"
            class="bi bi-arrow-up-circle-fill" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 0 0 8a8 8 0 0 0 16 0m-7.5 3.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707z" />
        </svg>
    </button> -->
</footer>

