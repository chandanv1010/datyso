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
                    <th scope="col" class="text-center" width="20%">Họ và tên</th>
                    <th scope="col" class="text-center" width="20%">Email</th>
                    <th scope="col" class="text-center" width="10%">Hồ sơ/CV</th>
                    <th scope="col" class="text-center" width="20%">Vị trí ứng tuyển</th>
                    <th scope="col" class="text-center" width="10%">Số điện thoại</th>
                    <th scope="col" class="text-center" width="10%">Trạng thái</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <th class="text-center"></th>
                        <td class="text-center">{{ $item->full_name }}</td>
                        <td class="text-center">{{ $item->email }}</td>
                        <td class="text-center"><a href="{{ Storage::url($item->file) }}" target="_blank">Download</a></td>
                        <td class="text-center">{{ $item->nominee }}</td>
                        <td class="text-center">{{ $item->phone }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch ms-4 d-flex justify-content-center"
                                style="display: flex;flex-direction: column;align-items: center;">
                                <form action="{{ route('admin.recruitment.statusApply', ['id' => $item->id]) }}" method="POST"
                                    id="form-status-{{ $item->id }}">
                                    @csrf
                                    <input type="checkbox"
                                        class="form-check-input status {{ $item->status == 1 ? 'active-item' : 'deactive-item' }}"
                                        name="status" data-value="{{ $item->id }}" id="status-{{ $item->id }}"
                                        {{ $item->status == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" type="hidden"
                                        for="status-{{ $item->id }}"></label>
                                </form>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.3em" height="1.3em" fill="currentColor"
                                    class="bi bi-info-circle text-dark mt-n1" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path
                                        d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="">
                                    <div class="container bg-light py-4">
                                        <h4 class="text-center fw-bolder text-color">Thông tin ứng viên</h4>
                                        <div class="my-3">
                                            <div class="row mb-3">
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Email</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="email" name="email" value="{{ $item->email }}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Họ và tên</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="full_name" name="full_name" value="{{ $item->full_name }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Vị trí ứng tuyển</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="nominee" name="nominee" value="{{ $item->nominee }}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Cấp bậc đào tạo</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="level" name="level" value="{{ $item->level }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Tên trường</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="school_name" name="school_name" value="{{ $item->school_name }}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Chuyên ngành</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="specialized" name="specialized" value="{{ $item->specialized }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Công việc gần nhất</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="latest_job" name="latest_job" value="{{ $item->latest_job }}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Năm kinh nghiệm</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="years_experience" name="years_experience" value="{{ $item->years_experience }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Mức lương mong muốn</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="desired_salary" name="desired_salary" value="{{ $item->desired_salary }}">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="title" class="form-label text-color fw-semibold">Số điện thoại</label>
                                                    <input type="text" class="form-control rounded-0 p-2" disabled id="phone" name="phone" value="{{ $item->phone }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
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
