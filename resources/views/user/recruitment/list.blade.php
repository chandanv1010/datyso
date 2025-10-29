@extends('user.main')
@section('templateContent')
    <!-- Huong Dan Section -->
    <div id="event-section" class="container mt-3">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a></li>

                <li class="breadcrumb-item"><a href="#" class="no-active">Tuyển dụng</a></li>
            </ol>
        </nav>
    </div>
    <div class="container-fluid p-0">
        <div class="">
            <div class="">
                <img src="{{ asset('asset/images/bg_1.jpg')}}" class="d-block w-100 height-banner object-fit-cover">
            </div>
        </div>
        <div class="scrollspy-example bg-white">
            <div class="pt-5 container">
                <div class="text-center text-uppercase">
                    <span class="border-bottom fw-bolder py-2 text-color fs-2">Tuyển dụng</span>
                </div>
                @include('admin.core.alert')
                @include('user.recruitment.core.filter')
                <div class="mt-4">
                    <div class="row">
                        @foreach ($items as $item)
                            <div class="col-sm-4 mb-3">
                                <div class="card border-0 rounded-4 bg-transparent h-100">
                                    <img src="{{ getImageUrl($item->image) }}"
                                        class="card-img-top rounded-4 w-100 shadow" alt="{{ $item->name }}"
                                        style="max-height: 12em">
                                    <div class="card-body py-2 px-0 d-flex flex-column">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <small class="fw-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em"
                                                        fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                                        <path
                                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                                    </svg>
                                                    {{ date_format($item->updated_at, 'd/m/Y') }}
                                                </small>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="float-end">
                                                    {{ $item->type }}
                                                </small>
                                            </div>
                                        </div>
                                        <p class="mb-0 fw-semibold mb-1 overflow-hidden text-nowrap"><a href="{{ route('recruitment.detail', ['recruitmentSlug' => $item->slug]) }}"
                                            class="text-decoration-none text-black">{{ $item->name }}</a></p>
                                        <span>{{ $item->location }}</span>
                                        <a href="{{ route('recruitment.detail', ['recruitmentSlug' => $item->slug]) }}"
                                            class="mt-auto text-decoration-none fw-medium fst-italic text-dark-blue"><small>Xem
                                                chi
                                                tiết</small></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="fs-4 pt-4 text-color fw-bolder container">
            Nếu chưa có vị trí phù hợp, xin vui lòng để lại thông tin ứng tuyển tại đây
        </div>
        <div class="py-5">
            @include('user.recruitment.core.form')
        </div>
    </div>
@endsection
@section('footer')
@endsection
