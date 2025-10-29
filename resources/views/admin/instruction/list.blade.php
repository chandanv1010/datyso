@section('headerTitle', 'Quản trị - Danh sách hướng dẫn')
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
                        <li class="breadcrumb-item active">Danh sách bài hướng dẫn </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white mx-3 p-3 shadow-sm">
        @include('admin.core.alert')
        <div class="d-flex justify-content-between">
            <h1 class="fs-3 m-0">
                Danh sách hướng dẫn
            </h1>
            <a href="{{ route('admin.instruction.create') }}" class="btn btn-success">
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
                        <th scope="col" class="text-center" width="10%">Tên</th>
                        <th scope="col" class="text-center">Slug</th>
                        <th scope="col" class="text-center" width="20%">Ảnh</th>
                        <th scope="col" class="text-center" width="100">Trạng thái</th>
                        <th width="60"></th>
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($instructions as $instruction)
                        <tr>
                            <th class="text-center"></th>
                            <td class="text-center">
                                {{ $instruction->name }}</td>
                            <td class="text-center">
                                {{ $instruction->slug }}</td>
                            <div class="col-ms-4">
                                <td class="text-center">
                                    <img style="width:100%" src="{{ $instruction->image }}"
                                        alt="Instruction Image">
                                </td>
                            </div>
                            
                            <td class="text-center">
                                <div class="form-check form-switch ms-4 d-flex justify-content-center"
                                    style="display: flex;flex-direction: column;align-items: center;">
                                    <form action="{{ route('admin.instruction.status', ['id' => $instruction->id]) }}"
                                        method="POST" id="form-status-{{ $instruction->id }}">
                                        @csrf
                                        <input type="checkbox"
                                            class="form-check-input status {{ $instruction->status == 1 ? 'active-item' : 'deactive-item' }}"
                                            name="status" data-value="{{ $instruction->id }}"
                                            id="status-{{ $instruction->id }}"
                                            {{ $instruction->status == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" type="hidden"
                                            for="status-{{ $instruction->id }}"></label>
                                    </form>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#info{{ $instruction->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em"
                                        fill="currentColor" class="bi bi-info-circle text-dark mt-n1" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                    </svg>
                                </a>
                                <div class="modal fade" id="info{{ $instruction->id }}" tabindex="-1"
                                    aria-labelledby="info{{ $instruction->id }}Label" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5 fw-bold" id="info{{ $instruction->id }}Label">
                                                    Thông tin
                                                    chi tiết</h1>
                                                <button type="button" class="btn-close p-3" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div id="custom-content">
                                                    <p class="fw-medium fw-bold mb-0">Nội dung:</p>
                                                    <span>{!! $instruction->content !!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href='{{ route('admin.instruction.update', ['id' => $instruction->id]) }}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em"
                                        fill="currentColor" class="bi bi-pencil-square text-dark mt-n1" viewBox="0 0 16 16"
                                        data-toggle="tooltip" data-placement="top" title="Cập nhập">
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
    </div>
@endsection
@section('footer')
    <script src="{{ asset('asset/js/datatable.js') }}"></script>
    <script src="{{ asset('asset/js/status.js') }}"></script>
@endsection
