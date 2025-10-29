@extends('user.main')
@section('templateContent')
    <div class="bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- MAIN BODY -->
    <div class="w-100">
        <img src="{{ asset('asset/images/bg-contact.webp') }}" class="w-100 position-relative" alt="">
    </div>
    <main class="bg-light p-5">
        <div class="container">
            <div class="text-center contact-title mb-5 text-uppercase fw-bold fs-1">Liên Hệ Với Chúng Tôi Để Được Tư Vấn Miễn Phí
            </div>
            @include('admin.core.alert')
            <div class="row shadow border border-0">
                <!-- form đăng ký -->
                <div class="col-sm-6 p-5 contact-bg" style="background-color: #002ab0;">
                    <div class="text-light px-5">
                        <!-- Logo -->
                        <div class="mb-5">
                            <img src="{{ asset('asset/images/logotrang.jpg') }}" class="w-100" alt="">
                        </div>
                        
                        <!-- Company Name -->
                        <div class="text-uppercase fs-5 fw-bold mb-2">Công Ty Cổ Phần Datyso Việt Nam</div>
                        
                        <!-- Contact Info -->
                        <div class="contact-info">

                            <!-- Email -->
                            <div class="mb-2 d-flex">
                                <img src="{{ asset('asset/images/em.png') }}" alt="Email" class="contact-icon">
                                <div class="mx-2"></div>
                                <span>Datysojsc@gmail.com</span>
                            </div>

                            <!-- Phone -->
                            <div class="mb-2 d-flex">
                                <img src="{{ asset('asset/images/phone.png') }}" alt="Phone" class="contact-icon">
                                <div class="mx-2"></div>
                                <span>0963.535.196</span>
                            </div>

                            <!-- Address -->
                            <div class="mb-2 d-flex">
                                <img src="{{ asset('asset/images/address.png') }}" alt="Address" class="contact-icon">
                                <div class="mx-2"></div>
                                <span>Số 245 Phố Thú Y, Đức Thượng, Hoài Đức, Hà Nội</span><br>
                            </div>

                            <!-- Toll-Free Number -->
                            <div class="mb-2 d-flex">
                                <div class="mx-2"></div>
                                <span class="phone-number">0963535196</span>
                            </div>
                        </div>
                    </div>
                </div>

                

                <!-- Form HubSpot mới tạo -->
                <div class="col-sm-6 bg-white p-5">
                    <div class="contact">
                        <script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/embed/v2.js"></script>
                        <script>
                          hbspt.forms.create({
                            portalId: "47825976",
                            formId: "8d62a10b-5639-4f1c-bfec-123bccb1257e"
                          });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </main>

      <!-- Add CSS here -->
 <style>
.row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap; /* Đảm bảo responsive */
}

.col-sm-6 {
    flex: 1;
    margin-right: 10px; /* Thêm khoảng cách giữa hai phần */
}

.contact-bg {
    background-color: #002ab0;
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-right: 15px; /* Điều chỉnh để tạo khoảng cách */
}

.contact-info {
    margin-top: 20px;
}

.contact-info .d-flex {
    align-items: center;
    margin-bottom: 10px;
}

.contact-icon {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.phone-number {
    background-color: white;
    color: #002ab0;
    font-size: 54px; /* Tăng kích thước chữ cho số điện thoại */
    padding: 20px 40px; /* Tăng padding để số điện thoại có không gian rộng hơn */
    border-radius: 20px;
}


.contact-info span {
    font-size: 20px;
    font-weight: normal; /* Thay 'bold' bằng 'normal' để bỏ in đậm */
}


.contact-form {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.contact-icon {
    width: 35px;  /* Tăng kích thước icon */
    height: 35px; /* Tăng kích thước icon */
    margin-right: 15px; /* Tăng khoảng cách giữa icon và văn bản */
}

@media (max-width: 768px) {
    .col-sm-6 {
        flex: 0 0 100%; /* Khi màn hình nhỏ, mỗi form sẽ chiếm toàn bộ chiều rộng */
        margin-right: 0;
    }
}
</style>
@endsection
@section('footer')
@endsection
