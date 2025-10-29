@extends('user.main')
@section('templateContent')

    <div id="hero-image" style="background-image: url('{{ $category->banner ?? asset('asset/images/category_empty.jfif') }}')">
        <div class="d-flex justify-content-center align-items-center h-100 text-white text-center"
            style="background-color: rgba(0, 0, 0, 0.6); ">
            <h1 style="font-size: 3rem; font-weight: 700;">{{ $category->name }}</h1>
        </div>
    </div>

    <!-- Main Product Section -->
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Sản phẩm</li>
            </ol>
        </nav>

        <div class="mb-4">
            {{-- <nav class="navbar navbar-expand-lg bg-white border">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                    </svg>
                                    <h6 class="d-inline me-1 fw-medium">
                                        BẢO HÀNH
                                    </h6>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        fill="currentColor" class="bi bi-database mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                    </svg>
                                    <h6 class="d-inline me-1 fw-medium">
                                        KHỐI LƯỢNG:
                                    </h6>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        fill="currentColor" class="bi bi-lightning mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641zM6.374 1 4.168 8.5H7.5a.5.5 0 0 1 .478.647L6.78 13.04 11.478 7H8a.5.5 0 0 1-.474-.658L9.306 1z" />
                                    </svg>
                                    <h6 class="d-inline me-1 fw-medium">
                                        NGUỒN ĐIỆN VÀO:
                                    </h6>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        fill="currentColor" class="bi bi-award mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z" />
                                        <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z" />
                                    </svg>
                                    <h6 class="d-inline me-1 fw-medium">
                                        THƯƠNG HIỆU:
                                    </h6>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item"
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div> --}}

            <div class="row mb-4">
                <!-- Left Sidebar Danh Muc -->
                <div class="col-sm-3 mb-3">
                    <div class="border-0 shadow rounded-4">
                        <h6 class="ps-3 py-3 mb-0" style="background-color: #002ab0; color: white;">DANH MỤC SẢN PHẨM
                        </h6>
                        <div id="list-danh-muc" class="py-2" style="background-color: white;">
                            @foreach ($categories as $categoryItem)
                                <div class="px-3 py-1">
                                    <div class="d-flex py-1">
                                        @if (count($categoryItem->child) > 0)
                                            <h6 data-bs-toggle="collapse" style="cursor: pointer"
                                                data-bs-target="#collapse-{{ $categoryItem->id }}" aria-expanded="false"
                                                aria-controls="collapse-{{ $categoryItem->id }}"
                                                data-choice="{{ $category->id == $categoryItem->id || $categoryItem->child()->find($category->id) ? 'true' : 'false' }}"
                                                class="danh-muc-title text-dark-blue fw-semibold ms-1 mb-0">
                                                {{ $categoryItem->name }}
                                            </h6>
                                        @else
                                            <a href="{{ route('product.list', ['categorySlug' => $categoryItem->slug]) }}"
                                                class="text-decoration-none danh-muc-title text-dark-blue fw-semibold ms-1 mb-0">{{ $categoryItem->name }}</a>
                                        @endif
                                    </div>
                                    <!-- Subcategories -->
                                    @if (count($categoryItem->child) > 0)
                                        <div class="collapse {{ $category->id == $categoryItem->id || $categoryItem->child()->find($category->id) ? 'show' : '' }}"
                                            id="collapse-{{ $categoryItem->id }}">
                                            <div class="card card-body p-2" style="border: none;">
                                                @foreach ($categoryItem->child as $child)
                                                    <h5 class="danh-muc-con pt-0 m-0">
                                                        <a class="dropdown-item fw-semibold"
                                                            href="{{ route('product.list', ['categorySlug' => $child->slug]) }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" class="bi bi-dot"
                                                                viewBox="0 0 16 16" style="color: #002ab0;">
                                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                                                            </svg>
                                                            {{ $child->name }}
                                                        </a>
                                                    </h5>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-sm-9" id="product-container">
                    <div class="row mb-4">
                        @foreach ($products as $product)
                            <div class="col-sm-4 mb-3">
                                <div class="h-100">
                                    <a href="{{ route('product.detail', ['categorySlug' => $category->slug, 'productSlug' => $product->slug]) }}"
                                        class="text-decoration-none text-black h-100">
                                        <div class="bg-white border-0 rounded-4 shadow d-flex flex-column h-100">
                                            @foreach ($product->images as $image)
                                                <img src="{{ getImageUrl($image->image) }}" alt="{{ $product->name }}"
                                                    class="w-100 rounded-top-4" style="height: 17em">
                                            @endforeach
                                            <div class="p-3">
                                                <span class="fw-semibold mb-2">{{ $product->name }}</span>
                                            </div>
                                            <div class="p-3 mb-2 mt-auto">
                                                <!-- Product rating stars -->
                                                @for ($i = 0; $i < 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-star-fill mt-n1 text-warning"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="{{ $i < $product->rating ? 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z' : 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z' }}" />
                                                    </svg>
                                                @endfor
                                                <span>({{ $product->reviews_count }} đánh giá)</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="float-end mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
