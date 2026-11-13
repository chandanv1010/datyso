@extends('user.main')
@section('templateContent')
    <div class="bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- MAIN BODY -->
    <div class="w-100">
        <img src="{{ asset('asset/images/bg-contact.webp') }}" class="w-100 position-relative" alt="Liên hệ DATYSO">
    </div>
    <main class="bg-light p-3 p-md-5 contact-page-wrapper">
        <div class="container">
            <h1 class="text-center contact-title mb-5 text-uppercase fw-bold fs-1">Liên Hệ Với Chúng Tôi Để Được Tư Vấn Miễn Phí</h1>
            @include('admin.core.alert')
            <div class="row shadow border border-0 bg-white rounded-3 overflow-hidden">
                <!-- Thông tin liên hệ -->
                <div class="col-sm-6 p-4 p-md-5 contact-bg" style="background-color: #002ab0;">
                    <div class="text-light px-md-4">
                        <!-- Logo -->
                        <div class="mb-5">
                            <img src="{{ asset('asset/images/logotrang.jpg') }}" class="w-100" alt="DATYSO">
                        </div>

                        <!-- Company Name -->
                        <div class="text-uppercase fs-5 fw-bold mb-2">Công Ty Cổ Phần Datyso Việt Nam</div>

                        <!-- Contact Info -->
                        <div class="contact-info">
                            <!-- Email -->
                            <div class="mb-2 d-flex">
                                <img src="{{ asset('asset/images/em.png') }}" alt="Email" class="contact-icon">
                                <div class="mx-2"></div>
                                <span>support@datyso.vn</span>
                            </div>

                            <!-- Phone -->
                            <div class="mb-2 d-flex">
                                <img src="{{ asset('asset/images/phone.png') }}" alt="Phone" class="contact-icon">
                                <div class="mx-2"></div>
                                <span>0968945633</span>
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
                                <span class="phone-number">0968945633</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Liên hệ trực tiếp (DATYSO Lead Form) -->
                <div class="col-sm-6 bg-white p-4 p-md-5 rounded-end-3">
                    <div class="contact">
                        @php
                            $contactSourceType = isset($product) && $product ? 'product' : ((isset($category) && $category) ? 'category' : 'contact');
                            $contactSourceName = isset($product) && $product ? $product->name : ((isset($category) && $category) ? $category->name : '');
                            $contactValue = isset($product) && $product ? (float) ($product->price_discount ?: ($product->price ?: 0)) : 0;
                        @endphp
                        <x-lead-quote-form
                            :source-type="$contactSourceType"
                            :product-id="$product?->id"
                            :category-id="$category?->id"
                            :source-name="$contactSourceName"
                            :source-url="url()->full()"
                            :value="$contactValue"
                            :show-email="true"
                            :show-sidebar="false"
                        />
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add CSS here -->
    <style>
        .contact-page-wrapper .row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .contact-page-wrapper .lead-quote-section .row {
            margin-right: calc(-0.5 * var(--bs-gutter-x));
            margin-left: calc(-0.5 * var(--bs-gutter-x));
        }

        .contact-bg {
            background-color: #002ab0;
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .contact-info {
            margin-top: 20px;
        }

        .contact-info .d-flex {
            align-items: center;
            margin-bottom: 10px;
        }

        .phone-number {
            background-color: white;
            color: #002ab0;
            font-size: 54px;
            padding: 20px 40px;
            border-radius: 20px;
        }

        .contact-info span {
            font-size: 20px;
            font-weight: normal;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .contact-icon {
            width: 35px;
            height: 35px;
            margin-right: 15px;
        }

        @media (max-width: 768px) {
            .contact-page-wrapper .col-sm-6 {
                flex: 0 0 100%;
                margin-right: 0;
            }
            .phone-number {
                font-size: 32px;
                padding: 12px 24px;
            }
        }
    </style>
@endsection
@section('footer')
@endsection
