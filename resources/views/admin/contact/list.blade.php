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
    @if(isset($mailDiagnostics) && !$mailDiagnostics['ready'])
        <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2" role="alert">
            <div class="small">
                <strong>&#9888; Cảnh báo gửi Email Sales:</strong>
                {{ implode(' ', $mailDiagnostics['issues']) }}
            </div>
            <a href="{{ route('admin.system.index') }}" class="btn btn-sm btn-warning fw-semibold">
                Cấu hình Email / SMTP ngay
            </a>
        </div>
    @endif
    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">
            Danh sách liên hệ
        </h1>
    </div>

    <div class="table-responsive">
        <table id="myTable" class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center" width="5%">#</th>
                    <th scope="col" class="text-center">Khách hàng</th>
                    <th scope="col" class="text-center">SĐT &amp; Tỉnh/thành</th>
                    <th scope="col" class="text-center">Sản phẩm / Danh mục</th>
                    <th scope="col" class="text-center">Nhu cầu</th>
                    <th scope="col" class="text-center">Thời gian</th>
                    <th scope="col" class="text-center">Mail Sales</th>
                    <th scope="col" class="text-center">Trạng thái</th>
                    <th scope="col" class="text-center" width="10%">Xử lý</th>
                    <th scope="col" class="text-center" width="8%">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr>
                        <th class="text-center"></th>
                        <td>
                            <div class="fw-semibold">{{ $contact->username }}</div>
                            @if(!empty($contact->email))
                                <small class="text-muted">{{ $contact->email }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="tel:{{ $contact->phone }}" class="fw-bold text-primary text-decoration-none d-block">{{ $contact->phone }}</a>
                            @if(!empty($contact->province))
                                <small class="text-muted">{{ $contact->province }}</small>
                            @endif
                        </td>
                        <td>
                            @if(!empty($contact->source_name) || $contact->product || $contact->category)
                                <span class="badge bg-info text-dark mb-1">
                                    {{ ($contact->source_type ?? '') === 'category' ? 'Danh mục' : (($contact->source_type ?? '') === 'product' ? 'Sản phẩm' : 'Liên hệ') }}
                                </span>
                                <div class="small fw-semibold">
                                    {{ $contact->source_name ?: ($contact->product?->name ?? ($contact->category?->name ?? '')) }}
                                </div>
                            @else
                                <span class="text-muted small">Trang liên hệ</span>
                            @endif
                        </td>
                        <td>
                            <div class="small" style="max-width: 260px; white-space: pre-line;">{{ \Illuminate\Support\Str::limit($contact->demand ?: strip_tags((string) $contact->content), 120) }}</div>
                        </td>
                        <td class="text-center small text-nowrap">
                            {{ optional($contact->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center small">
                            @if($contact->mail_status === 'sent')
                                <span class="badge bg-success">Đã gửi</span>
                            @elseif($contact->mail_status === 'logged')
                                <span class="badge bg-warning text-dark" title="{{ $contact->mail_error }}">Chỉ ghi Log</span>
                            @elseif($contact->mail_status === 'failed')
                                <span class="badge bg-danger" title="{{ $contact->mail_error }}">Gửi lỗi</span>
                            @elseif($contact->mail_status === 'skipped')
                                <span class="badge bg-secondary" title="{{ $contact->mail_error }}">Chưa cấu hình</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                            <form action="{{ route('admin.contact.resendMail', ['id' => $contact->id]) }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 11px;">
                                    Gửi mail
                                </button>
                            </form>
                        </td>
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
                                            <h1 class="modal-title fs-5 font-weight-bold" id="info{{ $contact->id }}Label">Thông tin chi tiết Lead #{{ $contact->id }}</h1>
                                            <button type="button" class="btn-close p-3" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p><strong>Khách hàng:</strong> {{ $contact->username }}</p>
                                            <p><strong>Số điện thoại:</strong> <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></p>
                                            @if(!empty($contact->province))
                                                <p><strong>Tỉnh/Thành phố:</strong> {{ $contact->province }}</p>
                                            @endif
                                            @if(!empty($contact->email))
                                                <p><strong>Email:</strong> {{ $contact->email }}</p>
                                            @endif
                                            @if(!empty($contact->source_name))
                                                <p><strong>Nguồn quan tâm:</strong> {{ $contact->source_name }} ({{ $contact->source_type }})</p>
                                            @endif
                                            @if(!empty($contact->source_url))
                                                <p><strong>Trang gửi:</strong> <a href="{{ $contact->source_url }}" target="_blank" rel="noopener">{{ $contact->source_url }}</a></p>
                                            @endif
                                            <div id="custom-content" class="mb-3">
                                                <label class="fw-medium font-weight-bold">Nhu cầu / Nội dung:</label>
                                                <div class="border rounded p-2 bg-light">{!! nl2br(e($contact->demand ?: strip_tags((string) $contact->content))) !!}</div>
                                            </div>
                                            @if(!empty($contact->mail_status))
                                                <div class="mb-3 p-2 border rounded bg-light small">
                                                    <strong>Trạng thái Mail Sales:</strong>
                                                    <code>{{ $contact->mail_status }}</code>
                                                    @if($contact->mail_sent_at)
                                                        ({{ $contact->mail_sent_at->format('H:i:s d/m/Y') }})
                                                    @endif
                                                    @if(!empty($contact->mail_error))
                                                        <div class="text-danger mt-1">{{ $contact->mail_error }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if ($contact->product)
                                                <div>
                                                    <label class="fw-medium font-weight-bold">Sản phẩm liên kết:</label>
                                                    <div>
                                                        <a target="_blank" href="{{ route('admin.product.update', ['id' => $contact->product->id]) }}">{{ $contact->product->name }}</a>
                                                    </div>
                                                </div>
                                            @elseif ($contact->category)
                                                <div>
                                                    <label class="fw-medium font-weight-bold">Danh mục liên kết:</label>
                                                    <div>
                                                        <a target="_blank" href="{{ route('admin.category.update', ['id' => $contact->category->id]) }}">{{ $contact->category->name }}</a>
                                                    </div>
                                                </div>
                                            @endif
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
