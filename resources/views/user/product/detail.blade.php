@php
    $gallery = $product->album ?? [];
    $price = $product->price ?? 0;
    $price_discount = $product->price_discount ?? 0;

    $percent = 0;
    if (!empty($price) && !empty($price_discount) && $price_discount < $price) {
        $percent = round((($price - $price_discount) / $price) * 100);
    }
@endphp

@extends('user.main')
@section('templateContent')
    <div class="bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('product.list', ['categorySlug' => $category->slug]) }}"
                            class="text-decoration-none">Sản phẩm</a></li>
                    <li class="breadcrumb-item" aria-current="page" class="">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- MAIN BODY -->
    <div class="container py-5">
        <div class="row mb-3">
            <div class="col-sm-4 mb-3">
                <div class="product-detail-left">
                    @if($percent > 0)
                    <span class="percent">-{{ $percent }}%</span>
                    @endif

                    @if(count($gallery) == 0)
                    <div class="border-0 shadow-sm rounded-4 bg-white">
                        @foreach ($product->images as $img)
                            <img src="{{ getImageUrl($img->image) }}" class="w-100 rounded-4" alt="{{ $product->name }}">
                        @endforeach
                    </div>
                    @else 
                    <x-product-gallery :gallery="$gallery" :product="$product" />
                    @endif
                </div>
            </div>
            <div class="col-sm-8">
                <div class="border-0 shadow-sm rounded-4 bg-white p-3">
                    <h1 class="fs-2 fw-semibold">{{ $product->name }}</h1>
                    <a href="#danh-gia" class="text-decoration-none text-dark fw-semibold">
                        <div class="mb-3">
                            @for ($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-star-fill mt-n1 text-warning" viewBox="0 0 16 16">
                                    <path
                                        d="{{ $i < $product->rating ? 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z' : 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z' }}" />
                                </svg>
                            @endfor
                            <span>4.7</span>
                        </div>
                    </a>
                    <div class="my-3">
                        <span class="fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em" fill="currentColor"
                                class="bi bi-dot mb-1" viewBox="0 0 16 16">
                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                            </svg>Mô tả sản phẩm:
                        </span>
                        <div class="ms-2">
                            @foreach ($product->descriptions as $attribute)
                                <div class="px-2 fw-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        fill="currentColor" class="bi bi-check-lg text-danger mb-1 me-2"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                    </svg>{{ $attribute->content }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="my-3 d-flex align-items-baseline">
                        <span class="fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em" fill="currentColor"
                                class="bi bi-dot mb-1" viewBox="0 0 16 16">
                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                            </svg>Giá sản phẩm:
                        </span>
                        <div class="d-flex ms-2 fw-bold">
                            <p class="text-red"> {!! show_price($product->price, $product->price_discount) !!}</p>
                        </div>                     
                    </div>
                    @if($product->price === null || $product->price == 0)
                        <div class="my-3">
                            <a href="{{ route('contact.index', ['san-pham' => $product->slug]) }}" class="btn btn-dark-blue w-100 p-2 fs-4">Liên hệ ngay</a>
                        </div>
                    @else
                        <div class="my-3">
                            <a data-id="{{ $product->id }}" class="btn btn-dark-blue w-100 p-2 fs-4 btn-addtocart">Thêm vào giỏ hàng</a>
                        </div>
                    @endif

                    @php
                        $shareLink = route('product.detail', ['categorySlug' => $category->slug, 'productSlug' => $product->slug]);
                    @endphp
                    <x-share-social :model="$product" :shareLink="$shareLink" />
                    <div class="google-news text-center">
                        Theo dõi chúng tôi trên <a href="{{ $system['seo_google_news'] }}" target="_blank"><img class="alignnone wp-image-405" src="{{ asset('asset/Google_News.webp') }}" alt="" width="141" height="26"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main class="bg-white">
        <div class="mb-5 bg-light">
            <div class="container-fluid bg-white">
                <div class="container bg-white">
                    <div class="py-5 navbar-detail">
                        <div class="bg-light">
                            <ul class="nav nav-tabs border-bottom" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="des-tab" data-bs-toggle="tab"
                                        data-bs-target="#des-tab-pane" type="button" role="tab"
                                        aria-controls="des-tab-pane" aria-selected="true">
                                        <span class="pb-2 fw-semibold">
                                            Mô tả sản phẩm
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="param-tab" data-bs-toggle="tab"
                                        data-bs-target="#param-tab-pane" type="button" role="tab"
                                        aria-controls="param-tab-pane" aria-selected="false">
                                        <span class="pb-2 fw-semibold">
                                            Thông số kỹ thuật
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="accessory-tab" data-bs-toggle="tab"
                                        data-bs-target="#accessory-tab-pane" type="button" role="tab"
                                        aria-controls="accessory-tab-pane" aria-selected="false"><span
                                            class="pb-2 fw-semibold">Bộ
                                            phụ
                                            kiện</span></button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="video-tab" data-bs-toggle="tab"
                                        data-bs-target="#video-tab-pane" type="button" role="tab"
                                        aria-controls="video-tab-pane" aria-selected="false" video><span
                                            class="pb-2 fw-semibold">Video</span></button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="des-tab-pane" role="tabpanel"
                                    aria-labelledby="des-tab" tabindex="0">
                                        <div class="p-5 editor-content">
                                           {!! $product->description !!}
                                         </div>

                                    <!--<div class="btn w-100 justify-content-center">-->
                                    <!--    <button type="button" class="btn btn-primary rounded-0 toggle-btn">Xem-->
                                    <!--        thêm</button>-->
                                    <!--</div>-->
                                </div>
                                <div class="tab-pane fade" id="param-tab-pane" role="tabpanel"
                                    aria-labelledby="param-tab" tabindex="0">
                                    <div class="p-5 editor-content">
                                     {!! $product->accessory_detail !!}
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="accessory-tab-pane" role="tabpanel"
                                    aria-labelledby="accessory-tab" tabindex="0">
                                   <div class="p-5 editor-content">
                                   {!! $product->tech_detail !!}
                                 </div>

                                </div>
                                <div class="tab-pane fade" id="video-tab-pane" role="tabpanel"
                                    aria-labelledby="disabled-tab" tabindex="0">
                                    <div class="p-5">
                                        @if ($product->video)
                                            <iframe class="embed-responsive-item"
                                                src="{{ getYoutubeEmbedUrl($product->video) }}" allowfullscreen
                                                style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;"></iframe>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (count($otherProducts) > 0)
            <div class="container">
                <div class="container">
                    <div class="pb-3">
                        <span class="fs-3 text-uppercase fw-bolder">Sản phẩm Khác<span>
                    </div>
                    <div class="row mb-4">
                        @foreach ($otherProducts as $otherProduct)
                            <div class="col-sm-4 mb-3">
                                <div class="h-100">
                                    <a href="{{ route('product.detail', ['categorySlug' => $otherProduct->categories[0]->slug, 'productSlug' => $otherProduct->slug]) }}"
                                        class="text-decoration-none text-black h-100">
                                        <div class="bg-white border-0 rounded-4 shadow d-flex flex-column h-100">
                                            @foreach ($otherProduct->images as $image)
                                                <img src="{{ getImageUrl($image->image) }}"
                                                    alt="{{ $otherProduct->name }}" class="w-100 rounded-top-4"
                                                    style="height: 17em">
                                            @endforeach
                                            <div class="p-3">
                                                <span class="fw-semibold mb-2">{{ $otherProduct->name }}</span>
                                            </div>
                                            <div class="p-3 mb-2 mt-auto">
                                                <!-- Product rating stars -->
                                                @for ($i = 0; $i < 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-star-fill mt-n1 text-warning"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="{{ $i < $otherProduct->rating ? 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z' : 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z' }}" />
                                                    </svg>
                                                @endfor
                                                <span>({{ $otherProduct->reviews_count }} đánh giá)</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </main>
@endsection
@section('footer')
@endsection
