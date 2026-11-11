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
                                        {{-- Chỉ hiển thị đánh giá sao khi có lượt đánh giá thật (ẩn khối sao và "( đánh giá)" rỗng) --}}
                                        <x-product-rating :product="$product" wrapper-class="p-3 mb-2 mt-auto" />
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
