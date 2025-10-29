@section('headerTitle', 'Quản trị - ' . ($isUpdate ? 'Cập nhập' : 'Tạo mới') . ' mạng xã hội')
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
            <li class="breadcrumb-item active">
                <a href="{{ route('admin.social.index') }}"
                        class="text-decoration-none">Danh sách mạng xã hội</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $isUpdate ? 'Cập nhập' : 'Tạo mới' }}
            </li>
          </ol>
        </div>
      </div>
    </div>
</div>
<div class="bg-white p-3 shadow-sm">
    @include('admin.core.alert')
    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">{{ $isUpdate ? 'Cập nhập' : 'Tạo mới' }}  mạng xã hội </h1>
        <a href="{{ route('admin.social.index') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-list-ol"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z" />
                <path
                    d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z" />
            </svg>
            Danh sách
        </a>
    </div>
    <div class="">
        <form
            action="{{ isset($social) ? route('admin.social.update', ['id' => $social->id]) : route('admin.social.create') }}"
            enctype="multipart/form-data" method="post" id="social-form">
            @csrf
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">Tên<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name') ?? (isset($social) ? $social->name : '') }}">
                    <p class="help is-danger text-danger">{{ $errors->first('name') }}</p>
                </div>
                <div class="col-6">
                    <label for="title" class="form-label">Đường dẫn url<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="url" name="url"
                        value="{{ old('url') ?? (isset($social) ? $social->url : '') }}">
                    <p class="help is-danger text-danger">{{ $errors->first('url') }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="exampleFormControlTextarea1" class="form-label">Ảnh<span class="text-danger">*</span></label><br>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="4" cols="50" name="image"> {{ old('image') ?? (isset($social) ? $social->image : '') }}</textarea>
                    <p class="help is-danger text-danger">{{ $errors->first('image') }}</p>
                </div> 
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="status" class="form-label">Trạng thái<span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="active" value="1"
                                {{ old('status') ? (old('status') == 1 ? 'checked' : '') : ($isUpdate ? ($social->status == 1 ? 'checked' : '') : 'checked') }}>
                            <label class="form-check-label" for="active">Hoạt động</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inactive" value="2"
                                {{ old('status') ? (old('status') == 2 ? 'checked' : '') : ($isUpdate ? ($social->status == 2 ? 'checked' : '') : '') }}>
                            <label class="form-check-label" for="inactive">Tạm khóa</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" id="btn-button" class="btn btn-{{ isset($social) ? 'primary' : 'success' }}">
                    @isset($social)
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-pencil-square mt-n1" viewBox="0 0 16 16">
                            <path
                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-plus-lg mt-n1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z" />
                        </svg>
                    @endisset
                    {{ isset($social) ? 'Cập nhập' : 'Tạo mới' }}
                </button>
            </div>
        </form>
        @isset($social)
            <div class="d-flex justify-content-end">
                <form action="{{ route('admin.social.delete', ['id' => $social->id]) }}" id="form-{{ $social->id }}"
                    method="POST">
                    @csrf
                    <button type="button" class="btn btn-danger delete mt-2" data-value="{{ $social->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-trash-fill mt-n1" viewBox="0 0 16 16">
                            <path
                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" />
                        </svg>
                        Xóa dữ liệu
                    </button>
                </form>
            </div>
        @endisset
    </div>
</div>
@endsection
@section('footer')
    <script src="{{ asset('asset/js/datatable.js') }}"></script>
    @isset($social)
    <script src="{{ asset('asset/js/delete.js') }}"></script>
@endisset
<script>
    $('#social-form').on('submit', function() {
        $('#btn-button').attr('disabled', true);
    });
</script>
@endsection
