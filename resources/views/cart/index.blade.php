@extends('user.main')
@section('templateContent')
    <div class="cart-container">
        <div class="page-breadcrumb background">      
            <div class="uk-container uk-container-center">
                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
                    <li class="mr10">
                        <a href="/" title="Trang chủ">Trang chủ</a>
                    </li>
                    <li class="mr10">    
                        <span class="slash">/</span>
                    </li>
                    <li class="mr10">
                        <a href="gio-hang" title="Thanh toán">Thanh toán</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="uk-container uk-container-center">
            @if ($errors->any())
            <div class="uk-alert uk-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('cart.checkout') }}" class="uk-form form" method="post">
                @csrf
                <h2 class="heading-1"><span>Thông tin đặt hàng</span></h2>
                <div class="cart-wrapper">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-2-5">
                            <div class="panel-cart cart-left">
                                <div class="panel-head">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <h2 class="cart-heading">
                                            <span>Thông tin giao hàng</span>
                                        </h2>
                                    </div>
                                    
                                </div>
                                <div class="panel-body mb30">
                                    <div class="cart-information">
                                        <div class="uk-grid uk-grid-medium mb20">
                                            <div class="uk-width-large-1-2">
                                                <div class="form-row">
                                                    <input 
                                                        type="text"
                                                        name="fullname"
                                                        value="{{ old('fullname', $customerAuth->name ?? '') }}"
                                                        placeholder="Nhập vào Họ Tên"
                                                        class="input-text"
                                                    >
                                                </div>
                                            </div>
                                            <div class="uk-width-large-1-2">
                                                <div class="form-row">
                                                    <input 
                                                        type="text"
                                                        name="phone"
                                                        value="{{ old('phone', $customerAuth->phone ?? '') }}"
                                                        placeholder="Nhập vào Số điện thoại"
                                                        class="input-text"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row mb20">
                                            <input 
                                                type="text"
                                                name="address"
                                                value="{{ old('address', $customerAuth->address ?? '') }}"
                                                placeholder="Nhập vào địa chỉ: ví dụ đường Lạc Long Quân..."
                                                class="input-text"
                                            >
                                        </div>
                                        <div class="form-row">
                                            <input 
                                                type="text"
                                                name="description"
                                                value="{{ old('description') }}"
                                                placeholder="Ghi chú thêm (Ví dụ: Giao hàng vào lúc 3 giờ chiều)"
                                                class="input-text"
                                            >
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="uk-width-large-3-5">
                            <div class="panel-cart">
                                <div class="panel-head">
                                    <h2 class="cart-heading"><span>Đơn hàng</span></h2>
                                </div>
                                <div class="panel-body">
                                    @if(count($carts) && !is_null($carts) )
                                    <div class="cart-list">
                                        @foreach($carts as $keyCart => $cart)
                                            <div class="cart-item" data-pd={{ $cart->id }} >
                                                <div class="uk-grid uk-grid-medium"> 
                                                    <div class="uk-width-small-1-1 uk-width-medium-1-5">
                                                        <div class="cart-item-image">
                                                            <span class="image img-scaledown"><img src="{{ getImageUrl($cart->options->image) }}" alt="{{ $cart->name }}"></span>
                                                            <span class="cart-item-number">{{ $cart->qty }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-small-1-1 uk-width-medium-4-5">
                                                        <div class="cart-item-info">
                                                            <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                                            <div class="cart-item-action uk-flex uk-flex-middle uk-flex-space-between">
                                                                <div class="cart-item-qty mr10">
                                                                    <button type="button" class="btn-qty minus">-</button>
                                                                    <input 
                                                                        type="text" 
                                                                        class="input-qty" 
                                                                        value="{{ $cart->qty }}"
                                                                    >
                                                                    <input type="hidden" class="rowId" value="{{ $cart->rowId }}">
                                                                    <button type="button" class="btn-qty plus" >+</button>
                                                                </div>
                                                                <div class="cart-item-price">
                                                                    <div class="uk-flex uk-flex-space-between">
                                                                        <span class="cart-price-sale">đơn giá: {{ convert_price($cart->price, true) }}đ</span>
                                                                        @if($cart->options->price_original > $cart->options->price_discount && $cart->options->price_discount > 0)
                                                                        <span class="cart-price-old ml15">{{ convert_price($cart->options->price_original, true) }}đ</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="cart-item-remove" data-row-id="{{ $cart->rowId }}">
                                                                    <span>✕</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @else
                                    <div>Bạn chưa có sản phẩm nào trong giỏ hàng <a style="color: red;" href="/san-pham/danh-muc">Mua ngay</a></div>

                                    @endif
                                </div>
                               <div class="panel-foot mt30 pay">
                                    <div class="cart-summary mb20">
                                        <div class="cart-summary-item">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summay-title bold">Tổng tiền</span>
                                                <div class="summary-value cart-total">
                                                    {{ number_format(Cart::content()->sum(fn($item) => $item->qty * $item->price), 0, ',', '.') }}đ
                                                </div>
                                            </div>
                                        </div>
                                        <div class="buy-more">
                                            <a href="/san-pham/danh-muc" class="btn-buymore">Chọn thêm sản phẩm khác</a>
                                        </div>
                                    </div>
                                </div>
                                @if(count($carts) && !is_null($carts) )
                                <button type="submit" class="cart-checkout" value="create" name="create">Thanh toán đơn hàng</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('footer')
@endsection
