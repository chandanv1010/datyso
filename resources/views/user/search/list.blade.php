@extends('user.main')
@section('templateContent')
    <!-- Main Product Section -->
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
                <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Trang chủ</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Tìm kiếm</li>
            </ol>
        </nav>

        <div class="row my-4">
            <h2 class="pb-3">
                Dưới đây là kết quả có liên quan đến từ khóa: <span class="text-color fw-boder">{{ $key }}</span>
            </h2>
            <div id="product-container">
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-sm-4 mb-3">
                            <div class="h-100">
                                <a href="{{ route('product.detail', ['categorySlug' => $product->categories[0]->slug, 'productSlug' => $product->slug]) }}"
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
@endsection
@section('footer')
