@section('headerTitle', 'Quản trị - Danh sách nhân sự')
@extends('admin.layout')
@section('templateContent')
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="#" class="text-decoration-none">Trang chủ </a> 
            </li>
            <li class="breadcrumb-item active">Danh sách liên hệ</li>
          </ol>
        </div>
      </div>
    </div>
</div>
<div class="bg-white mx-3 p-3 shadow-sm">
    @include('admin.core.alert')
    <div class="d-flex justify-content-between">
        <h1 class="fs-3 m-0">
            Danh sách liên hệ
        </h1>
    </div>

    <div class="table-responsive">
        <table id="myTable" class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center" width="5%">#</th>.
                    <th scope="col" class="text-center">Tên người dùng</th>
                    <th scope="col" class="text-center">Email</th>
                    <th scope="col" class="text-center">Phone</th>
                    <th scope="col" class="text-center">Trạng thái</th>
                    <th scope="col" class="text-center"  width="10%">Xử lý</th>
                    <th scope="col" class="text-center" width="10%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr>
                        <th class="text-center"></th>
                        <td class="text-center">{{ $contact->username }}</td>
                        <td class="text-center">{{ $contact->email }}</td>
                        <td class="text-center">{{ $contact->phone }}</td>
                        <td class="text-center" style="padding: 7px 0 0 42px;">
                            <div class="form-check form-switch">
                                <form action="{{ route('admin.contact.status', ['id' => $contact->id]) }}" method="POST"
                                    id="form-status-{{ $contact->id }}">
                                    @csrf
                                    <input type="checkbox"
                                        class="form-check-input status {{ $contact->is_read == 1 ? 'active-item' : 'deactive-item' }}"
                                        name="is_read" data-value="{{ $contact->id }}" id="status-{{ $contact->id }}"
                                        {{ $contact->is_read == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" type="hidden"
                                        for="status-{{ $contact->id }}"></label>
                                </form>
                            </div>
                        </td>
                        <td class="text-center" style="padding: 7px 0 0 42px;">
                            <div class="form-check form-switch">
                                <form action="{{ route('admin.contact.supported', ['id' => $contact->id]) }}" method="POST"
                                    id="form-isVerified-{{ $contact->id }}">
                                    @csrf
                                    <input type="checkbox"
                                        class="form-check-input isVerified {{ $contact->is_supported == 1 ? 'active-item' : 'deactive-item' }}"
                                        name="is_supported" data-value="{{ $contact->id }}" id="isVerified-{{ $contact->id }}"
                                        {{ $contact->is_supported == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" type="hidden"
                                        for="isVerified-{{ $contact->id }}"></label>
                                </form>
                            </div>
                        </td>
                            <td class="text-center">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#info{{ $contact->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.3em" height="1.3em" fill="currentColor"
                                    class="bi bi-info-circle text-dark mt-n1" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path
                                        d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg>
                            </a>
                            <div class="modal fade" id="info{{ $contact->id }}" tabindex="-1"
                                aria-labelledby="info{{ $contact->id }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5 font-weight-bold" id="info{{ $contact->id }}Label">Thông tin
                                                chi tiết</h1>
                                            <button type="button" class="btn-close p-3" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div id="custom-content">
                                                <label for="" class="fw-medium font-weight-bold">Nội dung:</label>
                                                <span>{!! $contact->content !!}</span>
                                            </div>
                                            <div>
                                                <label for="">Thông tin sản phẩm</label>
                                                <div>
                                                    @if ($contact->product_id)
                                                        <a target="_blank" href="{{ route('admin.product.update', ['id' => $contact->product->id]) }}">{{ $contact->product->name }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('footer')
    <script src="{{ asset('asset/js/datatable.js') }}"></script>
    <script src="{{ asset('asset/js/status.js') }}"></script>
@endsection
