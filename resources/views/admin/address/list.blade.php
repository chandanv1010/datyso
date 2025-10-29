@section('headerTitle', 'Quản trị - Danh sách địa chỉ')
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
            <li class="breadcrumb-item active">Danh sách địa chỉ </li>
          </ol>
        </div>
      </div>
    </div>
</div>
<div class="bg-white p-3 shadow-sm">
    @include('admin.core.alert')
    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">
            Danh sách địa chỉ
        </h1>
        <a href="{{ route('admin.address.create') }}" class="btn btn-success">
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
                    <th scope="col" class="text-center" width="5%">#</th>.
                    <th scope="col" class="text-center">Tên</th>
                    <th scope="col" class="text-center">Địa chỉ</th>
                    <th scope="col" class="text-center">Email</th>
                    <th scope="col" class="text-center">Phone</th>
                    <th scope="col" class="text-center">Hotline</th>
                    <th scope="col" class="text-center">Trạng thái</th>
                    <th scope="col" class="text-center" width="10%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <th class="text-center"></th>
                        <td class="text-center">{{ $item->name }}</td>
                        <td class="text-center">{{ $item->address }}</td>
                        <td class="text-center">{{ $item->email }}</td>
                        <td class="text-center">{{ $item->phone }}</td>
                        <td class="text-center">{{ $item->hotline }}</td>
                        <td class="text-center"></>
                            <div class="form-check form-switch" style="display: flex;flex-direction: column;align-items: center;">
                                <form action="{{ route('admin.address.status', ['id' => $item->id]) }}" method="POST"
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
                            <a href='{{ route('admin.address.update', ['id' => $item->id]) }}'>
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
@endsection
@section('footer')
    <script src="{{ asset('asset/js/datatable.js') }}"></script>
    <script src="{{ asset('asset/js/status.js') }}"></script>
@endsection
