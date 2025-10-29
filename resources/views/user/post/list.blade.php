@extends('user.main')
@section('templateContent')
    <!-- Huong Dan Section -->
    <div id="event-section" class="container mt-3">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a></li>

                <li class="breadcrumb-item"><a href="#" class="no-active">Tin tức</a></li>
            </ol>
        </nav>
        <div class="mt-3">
            <div style="border-bottom: 2px solid #002ab0; ">
                <h6 class="d-inline text-white pb-1 px-2 pt-1 text-uppercase" style="background-color: #002ab0;">Tin tức</h6>
            </div>
            <div class="mt-3">
                <div class="row">
                    @foreach ($posts as $post)
                        <div class="col-sm-4 mb-3">
                            <div class="card border-0 rounded-4 bg-transparent h-100">
                                <img src="{{ getImageUrl($post->image) }}"
                                    class="card-img-top rounded-4 w-100 shadow" alt="{{ $post->name }}"
                                    style="max-height: 12em">
                                <div class="card-body py-2 px-0 d-flex flex-column">
                                    <small class="fw-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em"
                                            fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                            <path
                                                d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                        </svg>
                                        {{ date_format($post->updated_at, 'd/m/Y') }}
                                    </small>
                                    <p class="mb-0 fw-semibold mb-1 overflow-hidden text-nowrap"><a href="{{ route('post.detail', ['postSlug' => $post->slug]) }}"
                                            class="text-decoration-none text-black">{{ $post->name }}</a></p>
                                    <a href="{{ route('post.detail', ['postSlug' => $post->slug]) }}"
                                        class="mt-auto text-decoration-none fw-medium fst-italic text-dark-blue"><small>Xem
                                            chi
                                            tiết</small></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>

        @if (count($products) > 0)
            <div class="mt-3">
                <div style="border-bottom: 2px solid #002ab0;">
                    <h6 class="d-inline text-white pb-1 px-2 pt-1 text-uppercase" style="background-color: #002ab0;">Có thể
                        bạn quan tâm</h6>
                </div>
                <div class="row mb-4 mt-3">
                    @foreach ($products as $otherProduct)
                        <div class="col-sm-4 mb-3">
                            <div class="h-100">
                                <a href="{{ route('product.detail', ['categorySlug' => $otherProduct->categories()->first()->slug, 'productSlug' => $otherProduct->slug]) }}"
                                    class="text-decoration-none text-black h-100">
                                    <div class="bg-white border-0 rounded-4 shadow d-flex flex-column h-100">
                                        @foreach ($otherProduct->images as $image)
                                            <img src="{{ getImageUrl($image->image) }}" alt="{{ $otherProduct->name }}"
                                                class="w-100 rounded-top-4" style="height: 17em">
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
        @endif
    </div>
@endsection
@section('footer')
@endsection
