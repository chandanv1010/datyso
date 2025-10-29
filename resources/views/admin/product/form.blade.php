@section('headerTitle', 'Quản trị - ' . ($isUpdate ? 'Cập nhập' : 'Tạo mới') . 'sản phẩm')
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
            <li class="breadcrumb-item active">
                <a href="{{ route('admin.product.index') }}"
                        class="text-decoration-none">Danh sách sản phẩm</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $isUpdate ? 'Cập nhập' : 'Tạo mới' }}
            </li>
          </ol>
        </div>
      </div>
    </div>
</div>
<div class="bg-white p-3 shadow-sm">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">{{ $isUpdate ? 'Cập nhập' : 'Tạo mới' }}  sản phẩm </h1>
        <a href="{{ route('admin.product.index') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-list-ol"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z" />
                <path
                    d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z" />
            </svg>
            Danh sách
        </a>
    </div>
    <div class="">
        <form
            action="{{ isset($item) ? route('admin.product.update', ['id' => $item->id]) : route('admin.product.create') }}"
            enctype="multipart/form-data" method="post" id="product-form">
            @csrf
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">Tên sản phẩm<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name') ?? (isset($item) ? $item->name : '') }}">
                    <p class="help is-danger text-danger">{{ $errors->first('name') }}</p>
                </div>
                <div class="col-6">
                    <label for="category_id" class="form-label">Danh mục<span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="0">Chọn danh mục...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (old('category_id') ?? (isset($item) ? $item->category_id : '')) == $category->id ? 'selected' : '' }}
                                @if($category->parent_id == 0) style="font-weight:bold;" @endif>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="help is-danger text-danger">{{ $errors->first('category_id') }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <label for="title" class="form-label">Giá</label>
                    <input type="text" class="form-control int" name="price"
                        value="{{ old('price') ?? (isset($item) ? convert_price($item->price, true) : '') }}">
                    <p class="help is-danger text-danger">{{ $errors->first('price') }}</p>
                </div>
                <div class="col-3">
                    <label for="title" class="form-label">Giá Discount</label>
                    <input type="text" class="form-control int" name="price_discount"
                        value="{{ old('price_discount') ?? (isset($item) ? convert_price($item->price_discount, true) : '') }}">
                    <p class="help is-danger text-danger">{{ $errors->first('price_discount') }}</p>
                </div>
                <div class="col-6">
                    <label for="title" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug"
                        value="{{ old('slug') ?? (isset($item) ? $item->slug : '') }}">
                    <p class="help is-danger text-danger">{{ $errors->first('slug') }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="image" class="form-label">Ảnh <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <div class="position-relative border rounded me-3">
                            @if (isset($item) && $item->images)
                                @foreach ($item->images as $value)
                                <img src="{{ $value['image'] }}" class="add-image" id="imgPreview" style="max-width: 300px">
                                <input type="hidden" id="imageCheck" name="imageCheck" value="1">
                                @endforeach
                            @else
                                <img src="{{ asset('asset/images/test.jpg') }}" class="add-image rounded" id="imgPreview" style="max-width: 300px">
                            @endif
                            <input type='button' id='remove' value='x' class="btn btn-light position-absolute top-0 end-0 mt-2 me-2" />
                        </div>
                        <div class="align-self-start input-group mb-3">
                            <input type="file" class="form-control files images" name="image" id="imgInp" value="{{ old('image', isset($item) ? $item->image : '') }}">
                            <label class="input-group-text" for="imgInp">Upload</label>
                        </div>
                    </div>
                    <p class="help is-danger text-danger">{{ $errors->first('image') }}</p>
                </div>    
            </div>
        
            <div class="row mb-3">
                <div class="col-6">
                    <label for="attribute" class="form-label">Thông tin sản phẩm <span class="text-danger">*</span></label>
                    <div class="d-flex w-100 mb-2">
                       
                        @if (old('attributeContent'))
                            <input type="text" class="form-control me-2" placeholder="Nội dung"
                            name="attributeContent[]"  value="{{ old('attributeContent')[0] }}">
                        @elseif(isset($item) && $item->descriptions->isNotEmpty())
                            <input type="text" class="form-control me-2" placeholder="Nội dung"
                            name="attributeContent[]"  value="{{ $item->descriptions[0]->content }}">
                        @else
                            <input type="text" class="form-control me-2" placeholder="Nội dung"
                            name="attributeContent[]" >
                        @endif
                            
                        <button type="button" class="btn btn-success" id="add">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="d-flex flex-column" id="attribute">
                        @if (old('attributeContent'))
                            @foreach (old('attributeContent') as $index => $content)
                                @if ($index != 0)
                                <div class="d-flex w-100 mb-2 inputDelete">
                                    <input type="text" class="form-control me-2" placeholder="Nội dung1"
                                        name="attributeContent[]" value="{{ $content }}">
                                    <button type="button" class="btn btn-danger onDelete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                        </svg>
                                    </button>
                                </div>
                                @endif
                            @endforeach
                        @elseif(isset($item) && $item->descriptions->isNotEmpty())
                            @foreach ($item->descriptions as $index => $description)
                                @if ($index != 0)
                                <div class="d-flex w-100 mb-2 inputDelete">
                                    <input type="text" class="form-control me-2" placeholder="Nội dung"
                                        name="attributeContent[]" value="{{ $description->content }}">
                                    <button type="button" class="btn btn-danger onDelete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                        </svg>
                                    </button>
                                </div>
                                @endif
                            @endforeach
                        @endif    
                    </div>
                    
                    @if ($errors->has('attributeContent.0'))
                        <div class="">
                            <p class="help is-danger text-danger">{{ $errors->first('attributeContent.0') }}</p>
                        </div>
                    @endif
                </div>   
                <div class="col-6">
                    <label for="text" class="form-label"> Điện đầu vào
                    </label><br>
                    <input type="text" class="form-control" id="voltage" name="voltage"
                    value="{{ old('voltage') ?? (isset($item) ? $item->voltage : '') }}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="text" class="form-label">Nội dung sản phẩm <span class="text-danger">*</span>
                    </label><br>
                    <textarea id="editor-area" class="form-control" name="description">
                        {{ old('description') ?? (isset($item) ? $item->description : '') }}
                    </textarea>
                    <p class="help is-danger text-danger">{{ $errors->first('description') }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="text" class="form-label">Chi tiết công nghệ <span class="text-danger">*</span>
                    </label><br>
                    <textarea id="editor-area" class="form-control" name="accessory_detail">
                        {{ old('accessory_detail') ?? (isset($item) ? $item->accessory_detail : '') }}
                    </textarea>
                    <p class="help is-danger text-danger">{{ $errors->first('accessory_detail') }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="text" class="form-label">Chi tiết phụ kiện <span class="text-danger">*</span>
                    </label><br>
                    <textarea id="editor-area" class="form-control" name="tech_detail">
                        {{ old('tech_detail') ?? (isset($item) ? $item->tech_detail : '') }}
                    </textarea>
                    <p class="help is-danger text-danger">{{ $errors->first('tech_detail') }}</p>
                </div>
            </div>

            <div class="mb-3">
                <div class="col-12">
                    <label for="exampleFormControlTextarea1" class="form-label">Video</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="4" cols="50" name="video"> {{ old('video') ?? (isset($item) ? $item->video : '') }}</textarea>
                    <p class="help is-danger text-danger">{{ $errors->first('video') }}</p>
                </div> 
            </div>

            <x-album :model="$item" />

            <div class="row mb-3">
                <div class="col-6">
                    <label for="status" class="form-label">Trạng thái<span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="active" value="1"
                                {{ old('status') ? (old('status') == 1 ? 'checked' : '') : ($isUpdate ? ($item->status == 1 ? 'checked' : '') : 'checked') }}>

                            <label class="form-check-label" for="active">Hoạt động</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inactive" value="2"
                                {{ old('status') ? (old('status') == 2 ? 'checked' : '') : ($isUpdate ? ($item->status == 2 ? 'checked' : '') : '') }}>
                            <label class="form-check-label" for="inactive">Tạm khóa</label>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="user_id" value="{{ Auth::guard('admin')->user()->id }}">
            </div>
            <div class="text-end">
                <button type="submit" id="btn-button" class="btn btn-{{ isset($item) ? 'primary' : 'success' }}">
                    @isset($item)
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-pencil-square mt-n1" viewBox="0 0 16 16">
                            <path
                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-plus-lg mt-n1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z" />
                        </svg>
                    @endisset
                    {{ isset($item) ? 'Cập nhập' : 'Tạo mới' }}
                </button>
            </div>
        </form>
        @isset($item)
            <div class="d-flex justify-content-end">
                <form action="{{ route('admin.product.delete', ['id' => $item->id]) }}" id="form-{{ $item->id }}"
                    method="POST">
                    @csrf
                    <button type="button" class="btn btn-danger delete mt-2" data-value="{{ $item->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-trash-fill mt-n1" viewBox="0 0 16 16">
                            <path
                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" />
                        </svg>
                        Xóa dữ liệu
                    </button>
                </form>
            </div>
        @endisset
    </div>
</div>
@endsection
@section('footer')
    <script src="https://cdn.tiny.cloud/1/pzn93bm0siit4expzgq6iwujj6bwzals3yvy416i2glwpq0f/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src=" {{ asset('asset/js/tinymce.js') }}"></script>

    <script src="{{ asset('asset/js/datatable.js') }}"></script>
    @isset($item)
    <script src="{{ asset('asset/js/delete.js') }}"></script>
@endisset

<script>
    $(document).on('change', '.images', function() {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $("#imgPreview").attr("src", event.target.result);
                $("#imageCheck").val('1');
            };
            reader.readAsDataURL(file);
        }
    });

    $(document).ready(function() {
        $("#imgInp").change(function(){
            readURL(this);
            $("#imageCheck").val('1');
        });

        $("#remove").click(function() {
            $("#imgInp").val('');
            $("#imgPreview").attr('src', '{{ asset('asset/images/test.jpg') }}');
            $("#imageCheck").val('0');
        });
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imgPreview').attr('src', e.target.result).removeClass('d-none').fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }   
    }
    

    $(document).ready(function() {
        $('#imgInp').change(function() {
            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                    $('#blah').removeClass('d-none');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    });

    $('#product-form').on('submit', function() {
        $('#btn-button').attr('disabled', true);
    });

     $(document).ready(function() {
        $("#add").on("click", function() {
            const getCurrentTime = (new Date()).getTime();
            let inputContent = '<div class="d-flex w-100 mb-2 inputDelete" id="inputDelete_' + getCurrentTime + '">' +
                '<input type="text" class="form-control me-2" placeholder="Nội dung" name="attributeContent[]">' +
                '<button type="button" class="btn btn-danger onDelete" id=' + getCurrentTime + '>' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">' +
                '<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>' +
                '</svg>' +
                '</button>' +
                '</div>';
            $('#attribute').append(inputContent);
        });

        // Remove dynamic input fields
        $(document).on("click", ".onDelete", function() {
            $(this).closest('.inputDelete').remove();
        });
    });
</script>
@endsection
