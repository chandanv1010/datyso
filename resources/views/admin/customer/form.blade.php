@section('headerTitle', 'Quản trị - Danh sách người dùng')
@extends('admin.layout')
@section('templateContent')
    <div class="bg-white p-3 shadow-sm">
        <div class="d-flex justify-content-between mb-3">
            <h1 class="fs-3 m-0">Đăng ký người dùng</h1>
            <a href="{{ route('admin.customer.index') }}" class="btn btn-primary">
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
                action="{{ isset($item) ? route('admin.customer.update', ['id' => $item->id]) : route('admin.customer.create') }}"
                enctype="multipart/form-data" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="first_name" class="form-label">Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                            value="{{ old('first_name') ?? (isset($item) ? $item->first_name : '') }}">
                        <p class="help is-danger text-danger first_name">{{ $errors->first('first_name') }}</p>
                    </div>

                    <div class="col-6">
                        <label for="last_name" class="form-label">Họ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                            value="{{ old('last_name') ?? (isset($item) ? $item->last_name : '') }}">
                        <p class="help is-danger text-danger last_name">{{ $errors->first('last_name') }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email') ?? (isset($item) ? $item->email : '') }}">
                        <p class="help is-danger text-danger email">{{ $errors->first('email') }}</p>
                    </div>
                    <div class="col-6">
                        <label for="password" class="form-label">Mật khẩu<span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password"
                            value="{{ old('password') ?? (isset($item) ? $item->password : '') }}">
                        <p class="help is-danger text-danger">{{ $errors->first('password') }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ old('phone') ?? (isset($item) ? $item->phone : '') }}">
                        <p class="help is-danger text-danger">{{ $errors->first('phone') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label for="status" class="form-label">Trạng thái<span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="active" value="1"
                                    {{ old('status') ? (old('status') == 1 ? 'checked' : '') : ($isUpdate ? ($item->status == 1 ? 'checked' : '') : 'checked') }}>
                                <label class="form-check-label" for="active">Tạm khóa </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="inactive" value="2"
                                    {{ old('status') ? (old('status') == 2 ? 'checked' : '') : ($isUpdate ? ($item->status == 2 ? 'checked' : '') : '') }}>
                                <label class="form-check-label" for="inactive">Hoạt động</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="is_verified" class="form-label">Xử lý<span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_verified" id="active"
                                    value="1"
                                    {{ isset($item) ? ($item->is_verified == 1 ? 'checked' : '') : 'checked' }}>
                                <label class="form-check-label" for="active">Chưa xử lý</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_verified" id="inactive"
                                    value="2" {{ isset($item) ? ($item->is_verified == 2 ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="inactive"> Đã xử lý</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" id="btn-button" class="btn btn-{{ isset($item) ? 'primary' : 'success' }}">
                        @isset($item)
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
                        {{ isset($item) ? 'Cập nhập' : 'Tạo mới' }}
                    </button>
                </div>
            </form>
            @isset($item)
                <div class="d-flex justify-content-end">
                    <form action="{{ route('admin.customer.delete', ['id' => $item->id]) }}" id="form-{{ $item->id }}"
                        method="POST">
                        @csrf
                        <button type="button" class="btn btn-danger delete mt-2" data-value="{{ $item->id }}">
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
    <script src="{{ asset('asset/js/status.js') }}"></script>
    @isset($item)
        <script src="{{ asset('asset/js/delete.js') }}"></script>
    @endisset
@endsection
