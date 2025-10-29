@section('headerTitle', 'Quản trị - Danh sách sản phẩm')
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
            <li class="breadcrumb-item active">Danh sách sản phẩm</li>
          </ol>
        </div>
      </div>
    </div>
</div>
<div class="bg-white p-3 shadow-sm">
    @include('admin.core.alert')
    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">
            Danh sách sản phẩm
        </h1>
        <a href="{{ route('admin.product.create') }}" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                class="bi bi-plus-lg mt-n1" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z" />
            </svg>
            Tạo mới
        </a>
    </div>

    <div class="table-responsive">
        <table id="myTable" class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center" width="5%">#</th>
                    <th scope="col" class="text-center">Tên sản phẩm</th>
                    <th scope="col" class="text-center" width="200">Ảnh</th>
                    <th scope="col" class="text-center">Điện áp</th>
                    <th scope="col" class="text-center">Người tạo</th>
                    <th scope="col" class="text-center" width="10%">Trạng thái</th>
                    <th scope="col" class="text-center" width="10%"></th>
                    <th scope="col" class="text-center" width="10%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                @if ($item->del_flg == 2)
                    <tr class="table-danger">
                @else
                    <tr>
                @endif
                        <th class="text-center">{{ $item->id }}</th>
                        <td class="text-center"></>{{ $item->name }}</td>

                        <div class="col-ms-4">
                            <td class="text-center w-20">
                                @foreach ($item->images as $img)
                                    <img src="{{ $img->image }}" alt="Product Image" class="w-100">
                                @endforeach
                            </td>
                        </div>
                        <td class="text-center"></>{{ $item->voltage }}</td>
                        <td class="text-center"></>
                            @foreach ($users as $user)
                                @if ($user->id == $item->user_id)
                                    {{ $user->username }} 
                                @endif
                            @endforeach
                        </td>

                        <td class="text-center"></>
                            <div class="form-check form-switch" style="display: flex;flex-direction: column;align-items: center;">
                                <form action="{{ route('admin.product.status', ['id' => $item->id]) }}" method="POST"
                                    id="form-status-{{ $item->id }}">
                                    @csrf
                                    <input type="checkbox"
                                        class="form-check-input status {{ $item->status == 1 ? 'active-item' : 'deactive-item' }}"
                                        name="status" data-value="{{ $item->id }}" id="status-{{ $item->id }}"
                                        {{ $item->status == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" type="hidden" for="status-{{ $item->id }}"></label>
                                </form>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#info{{ $item->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.3em" height="1.3em" fill="currentColor"
                                    class="bi bi-info-circle text-dark mt-n1" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path
                                        d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg>
                            </a>
                            <div class="modal fade" id="info{{ $item->id }}" tabindex="-1"
                                aria-labelledby="info{{ $item->id }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5 fw-bold" id="info{{ $item->id }}Label">Thông tin chi tiết:</h1>
                                            <button type="button" class="btn-close p-3" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <label for="" class="fw-medium fw-bold">Mô tả:</label>
                                            @foreach ($item->descriptions as $itemDescription)
                                                <div id="custom-content">
                                                    <span>{{ $itemDescription->content }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="modal-body text-start">
                                            <div id="custom-content">
                                                <label for="" class="fw-medium fw-bold">Nội dung:</label>
                                                <span>{!! $item->description !!}</span>
                                            </div>
                                        </div>

                                        <div class="modal-body text-start">
                                            <div id="custom-content">
                                                <label for="" class="fw-medium fw-bold">Chi tiết công nghệ:</label>
                                                <span>{!!  $item->tech_detail !!}</span>
                                            </div>
                                        </div>

                                        <div class="modal-body text-start">
                                            <div id="custom-content">
                                                <label for="" class="fw-medium fw-bold">Chi tiết phụ kiện:</label>
                                                <span>{!!  $item->accessory_detail !!}</span>
                                            </div>
                                        </div>

                                        <div class="modal-body text-start">
                                            <div id="custom-content">
                                                <label for="" class="fw-medium fw-bold">Video</label>
                                        
                                                @if ($item->video)
                                                    <div class="embed-responsive embed-responsive-16by9">
                                                        <iframe class="embed-responsive-item"
                                                                src="{{ getYoutubeEmbedUrl($item->video) }}"
                                                                allowfullscreen>
                                                        </iframe>
                                                    </div>
                                                @else
                                                    <p>Video không có sẵn.</p>
                                                @endif
                                        
                                            </div>
                                        </div>                                            
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href='{{ route('admin.product.update', ['id' => $item->id]) }}'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.3em" height="1.3em"
                                    fill="currentColor" class="bi bi-pencil-square text-dark mt-n1"
                                    viewBox="0 0 16 16" data-toggle="tooltip" data-placement="top" title="Cập nhập">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<style>
    .modal-body img {
    max-width: 100%;
    height: auto;
}
</style>
@endsection
@section('footer')
    <script src="{{ asset('asset/js/datatable.js') }}"></script>
    <script src="{{ asset('asset/js/status.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('[id^=info]').on('hidden.bs.modal', function () {
                var modalId = $(this).attr('id');
                var $iframe = $(this).find('iframe');
                var src = $iframe.attr('src');
                $iframe.attr('src', '');
                $iframe.attr('src', src);
            });
        });
    </script>
@endsection
