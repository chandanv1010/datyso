@extends('user.main')
@section('title', 'Đặt hàng thành công')
@section('templateContent')
 <div class="order-success-container">
    <div class="order-success-card">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" stroke-width="2" stroke="#4CAF50" fill="none"/>
                <path d="M8 12l3 3 5-6" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-message">Cảm ơn Bệ hạ đã tin tưởng và đặt hàng tại cửa hàng của chúng tôi 💛</p>

        <div class="order-info">
            <h2>Thông tin đơn hàng</h2>
            <ul>
                <li><strong>Mã đơn hàng:</strong> #{{ $order->id }}</li>
                <li><strong>Họ tên:</strong> {{ $order->fullname }}</li>
                <li><strong>Số điện thoại:</strong> {{ $order->phone }}</li>
                <li><strong>Địa chỉ:</strong> {{ $order->address }}</li>
                <li><strong>Trạng thái:</strong> <span class="status pending">{{ ucfirst($order->status) }}</span></li>
                <li><strong>Tổng tiền:</strong> <span class="total">{{ number_format($order->total, 0, ',', '.') }} ₫</span></li>
            </ul>
        </div>

        <div class="order-products">
            <h2>Sản phẩm đã đặt</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->products as $product)
                        <tr>
                            <td>{{ $product->name ?? 'Sản phẩm' }}</td>
                            <td>{{ $product->pivot->qty }}</td>
                            <td>{{ number_format($product->pivot->price, 0, ',', '.') }} ₫</td>
                            <td>{{ number_format($product->pivot->subtotal, 0, ',', '.') }} ₫</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="back-to-home">
            <a href="/">Tiếp tục mua sắm</a>
        </div>
    </div>
</div>
@endsection
@section('footer')
@endsection
