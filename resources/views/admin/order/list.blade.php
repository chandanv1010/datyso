@section('headerTitle', 'Quản trị - Danh sách đơn hàng')
@extends('admin.layout')
@section('templateContent')
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="#" class="text-decoration-none">Trang chủ</a>
            </li>
            <li class="breadcrumb-item active">Danh sách đơn hàng</li>
          </ol>
        </div>
      </div>
    </div>
</div>

<div class="bg-white p-3 shadow-sm">
    @include('admin.core.alert')

    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">Danh sách đơn hàng</h1>
    </div>

    <div class="table-responsive">
        <table id="myTable" class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" width="5%">#</th>
                    <th class="text-center">Tên khách hàng</th>
                    <th class="text-center" width="200">Số điện thoại</th>
                    <th class="text-center">Địa chỉ</th>
                    <th class="text-center" width="10%">Trạng thái</th>
                    <th class="text-center" width="10%">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $item)
                <tr>
                    <th class="text-center">{{ $item->id }}</th>
                    <td class="text-center">{{ $item->fullname }}</td>
                    <td class="text-center">{{ $item->phone }}</td>
                    <td class="text-center">{{ $item->address }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $item->status == 'Hoàn thành' ? 'success' : ($item->status == 'Đang xử lý' ? 'warning' : 'secondary') }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#orderInfo{{ $item->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.3em" height="1.3em" fill="currentColor"
                                class="bi bi-info-circle text-dark mt-n1" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                            </svg>
                        </a>

                        <!-- Modal -->
                        <div class="modal fade" id="orderInfo{{ $item->id }}" tabindex="-1" aria-labelledby="orderInfo{{ $item->id }}Label" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5 fw-bold" id="orderInfo{{ $item->id }}Label">
                                            Chi tiết đơn hàng #{{ $item->id }}
                                        </h1>
                                        <button type="button" class="btn-close p-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">

                                        <!-- Thông tin khách hàng -->
                                        <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>
                                        <ul class="list-unstyled mb-4">
                                            <li><strong>Họ và tên:</strong> {{ $item->fullname }}</li>
                                            <li><strong>Số điện thoại:</strong> {{ $item->phone }}</li>
                                            <li><strong>Email:</strong> {{ $item->email }}</li>
                                            <li><strong>Địa chỉ:</strong> {{ $item->address }}</li>
                                            <li><strong>Trạng thái:</strong> {{ $item->status }}</li>
                                            <li><strong>Ngày đặt hàng:</strong> {{ $item->created_at->format('d/m/Y H:i') }}</li>
                                        </ul>

                                        <!-- Danh sách sản phẩm -->
                                        <h5 class="fw-bold mb-3">Danh sách sản phẩm</h5>
                                        <div class="table-responsive mb-3">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" width="5%">#</th>
                                                        <th>Tên sản phẩm</th>
                                                        <th class="text-center" width="100">Số lượng</th>
                                                        <th class="text-center" width="120">Giá</th>
                                                        <th class="text-center" width="120">Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item->products as $index => $product)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $product->name }}</td>
                                                        <td class="text-center">{{ $product->pivot->qty }}</td>
                                                        <td class="text-center">{{ number_format($product->pivot->price, 0, ',', '.') }}đ</td>
                                                        <td class="text-center">{{ number_format($product->pivot->price * $product->pivot->qty, 0, ',', '.') }}đ</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Tổng kết -->
                                        <h5 class="fw-bold mb-3">Tổng kết</h5>
                                        <ul class="list-unstyled">
                                            <li><strong>Tạm tính:</strong> {{ number_format($item->subtotal, 0, ',', '.') }}đ</li>
                                            <li><strong>Giảm giá:</strong> {{ number_format($item->discount, 0, ',', '.') }}đ</li>
                                            <li><strong>Phí vận chuyển:</strong> {{ number_format($item->shipping_fee ?? 0, 0, ',', '.') }}đ</li>
                                            <li><strong>Tổng thanh toán:</strong> <span class="text-danger fw-bold">
                                                {{ number_format($item->subtotal - $item->discount + ($item->shipping_fee ?? 0), 0, ',', '.') }}đ
                                            </span></li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Modal -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.modal-body img {max-width: 100%; height: auto;}
.modal-content {border-radius: 10px;}
.table-sm td, .table-sm th {padding: 0.5rem;}
</style>
@endsection

@section('footer')
<script src="{{ asset('asset/js/datatable.js') }}"></script>
<script>
$(document).ready(function() {
    $('[id^=orderInfo]').on('hidden.bs.modal', function () {
        const iframe = $(this).find('iframe');
        if (iframe.length) {
            const src = iframe.attr('src');
            iframe.attr('src', '');
            iframe.attr('src', src);
        }
    });
});
</script>
@endsection
