@section('headerTitle', 'Quản trị - Danh sách slide')
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
            <li class="breadcrumb-item active">Danh sách slide </li>
          </ol>
        </div>
      </div>
    </div>
</div>
<div class="bg-white p-3 shadow-sm">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="fs-3 m-0">Đăng ký slide</h1>
    </div>
    <div class="">
        <div class="row" id="slide_template">
            @foreach ($items as $key => $item)
                <div class="col-12 slide-template" id="slide_image_template_{{ $key }}">
                    <div class="d-flex mb-3">
                        <div class="w-100 me-2">
                            <label for="image-{{ $key }}" class="form-label">Ảnh</label>
                            <input class="form-control files images" type="file" name="files"
                                id="image-{{ $key }}" data-order="{{ $key }}">
                            <input type="hidden" value="{{ $item->id }}" id="old-image-{{ $key }}">
                        </div>
                        <div class="">
                            <label for="formFile" class="form-label">Thao tác</label>
                            <button type="button" class="btn btn-danger text-nowrap w-100 removeTemplate"
                                data-value="{{ $key }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    fill="currentColor" class="bi bi-trash-fill mt-n1" viewBox="0 0 16 16">
                                    <path
                                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" />
                                </svg>
                                Xóa
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <img src="{{ $item->image_url }}"
                            id="review-image-{{ $key }}" class="w-100" alt="">
                    </div>
                    <hr class="mt-0">
                </div>
            @endforeach
        </div>
        <div class="text-end mt-3">
            <button class="btn btn-warning addTemplate">Thêm slide</button>
            <hr>
            <button class="btn btn-success" id="submit">
                Tạo mới/Cập nhật
            </button>
        </div>
        <img src="" alt="" id="imgPreview">
        <form enctype="multipart/form-data" method="post" class="d-none" id="submitForm">
            @csrf
        </form>
    </div>
</div>
@endsection
@section('footer')
    <script src="{{ asset('asset/js/image.js') }}"></script>
    <script>
        const defaulImage = "{{ asset('asset/images/slider_default.png') }}";
        let position = {{ count($items) }};

        $(document).ready(function() {
            // Thêm template
            $('.addTemplate').on('click', function() {
                addRawTemplate();
            });

            // Xóa template
            $('#slide_template').on('click', '.removeTemplate', function(event) {
                if ($('.slide-template').length <= 1) return;
                let templateData = $(this).attr('data-value');
                event.preventDefault();
                event.stopPropagation();
                $.confirm({
                    title: 'Thông báo',
                    content: 'Xác nhận xóa dữ liệu này?',
                    type: 'red',
                    buttons: {
                        confirm: {
                            text: 'Tiếp tục',
                            btnClass: 'btn-danger' + ' mr-2',
                            action: function() {
                                removeRawTemplate(templateData);
                            },
                        },
                        cancel: {
                            text: 'Hủy',
                            btnClass: 'btn-light',
                            action: function() {},
                        },
                    }
                });
            });

            $('#submit').on('click', function(e) {
                e.preventDefault();
                $(".loading").fadeIn();
                const imageInputs = $('.files');
                let formData = new FormData(document.getElementById("submitForm"));
                let imageExistArr = [];

                for (var imageIndex = 0; imageIndex < $('.slide-template').length; imageIndex++) {
                    if ($(imageInputs[imageIndex])[0]) {
                        let imageExist = $(imageInputs[imageIndex])[0].files;
                        let oldImage = $('#old-image-' + imageIndex).val();
                        if (imageExist.length > 0) {
                            formData.append('image_' + imageExistArr.length, imageExist[0]);
                            imageExistArr.push(imageExist[0]);
                        } else {
                            imageExistArr.push(0);
                        }
                        formData.append('old_' + imageIndex, oldImage);
                    }
                }
                formData.append('images', imageExistArr.length);

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.slide.createSlide') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(msg) {
                        location.reload();
                    },
                    fail: function(error) {
                        alert(error);
                    },
                    complete: function(error) {
                        $(".loading").removeClass("hidden").hide();
                    }
                });
            });
        });

        function addRawTemplate() {
            $('#slide_template').append(defaulContent(position));
            position++;
        }

        function removeRawTemplate(removePosition) {
            $('#slide_image_template_' + removePosition).remove();
            updatePositions();
        }

        function updatePositions() {
            $('.slide-template').each(function(index) {
                $(this).attr('id', 'slide_image_template_' + index);
                $(this).find('.files').attr('id', 'image-' + index).attr('data-order', index);
                $(this).find('.removeTemplate').attr('data-value', index);
                $(this).find('.slide-image').attr('id', 'review-image-' + index);
                $(this).find('.removeTemplate').attr('data-value', index);
                $(this).find('.removeTemplate').off('click').on('click', function(event) {
                    if ($('.slide-template').length <= 1) return;
                    let templateData = $(this).attr('data-value');
                    event.preventDefault();
                    event.stopPropagation();
                    $.confirm({
                        title: 'Thông báo',
                        content: 'Xác nhận xóa dữ liệu này?',
                        type: 'red',
                        buttons: {
                            confirm: {
                                text: 'Tiếp tục',
                                btnClass: 'btn-danger' + ' mr-2',
                                action: function() {
                                    removeRawTemplate(templateData);
                                    updatePositions();
                                },
                            },
                            cancel: {
                                text: 'Hủy',
                                btnClass: 'btn-light',
                                action: function() {},
                            },
                        }
                    });
                });
            });
        }

        function defaulContent(positionParam) {
            let content = '<div class="col-12 slide-template" id="slide_image_template_' + positionParam + '">';
            content += '<div class="d-flex mb-3">'
            content += '<div class="w-100 me-2">'
            content += '<label for="image-' + positionParam + '" class="form-label">Ảnh</label>'
            content += '<input class="form-control files images" type="file" name="files" id="image-' + positionParam +
                '" data-order="' + positionParam + '">'
            content += '<input type="hidden" value="0" id="old-image-' + positionParam + '">'
            content += '</div>'
            content += '<div class="">'
            content += '<label class="form-label">Thao tác</label>'
           
            content += '<button type="button" class="btn btn-danger text-nowrap w-100 removeTemplate" data-value="' +
                positionParam + '">'
            content +=
                '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash-fill mt-n1" viewBox="0 0 16 16"> <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" /></svg> Xóa'
            content += '</button>'
            content += '</div></div>'
            content += '<div class="mb-2">'
            content += '<img src="' + defaulImage + '" class="w-100 slide-image" id="review-image-' + positionParam +
                '" alt="">'
            content += '</div><hr class="mt-0"></div>';

            return content;
        }
    </script>
@endsection
