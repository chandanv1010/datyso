@extends('user.main')
@section('templateContent')

@include('user.slider', ['sliders' => $sliders])

<div data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-offset="0" class="scrollspy-example bg-white" tabindex="0">
    <div id="section1" class="pt-5 container">
        <div class="text-center text-uppercase mb-2">
            <span class="border-bottom fw-bolder py-2 text-introduce text-dark-blue fs-3">Giới thiệu chung</span>
        </div>
        <div class="row py-4">
            <div class="col-sm-6">
                <div class="text-uppercase text-center fs-3 mb-4">
                    <span class="text-dark-blue text-introduce fw-bold">Công ty cổ phần datyso việt nam</span>
                </div>
                <div class="row">
                    <div class="col-sm-6 introduce-top">
                        <span class="fw-bold">Diện tích</span>
                        <p class="text-dark-blue fs-4 fw-bolder">20.000m</p>
                        <span class="fw-bold">Cơ sở</span>
                        <p class="text-dark-blue fs-4 fw-bolder">3</p>
                        <span class="fw-bold">Nhân sự</span>
                        <p class="text-dark-blue fs-4 fw-bolder">200 +</p>
                    </div>
                    <div class="col-sm-6 introduce-top">
                        <span class="fw-bold">Doanh thu</span>
                        <p class="text-dark-blue fs-4 fw-bolder">1.000 tỷ</p>
                        <span class="fw-bold">Sản phẩm</span>
                        <p class="text-dark-blue fs-4 fw-bolder">1.000 +</p>
                        <span class="fw-bold">Khách hàng</span>
                        <p class="text-dark-blue fs-4 fw-bolder">10.000 +</p>
                    </div>
                </div>
                <div class="text-center button-detail-compaty py-5">
                    <div class="fw-bold mb-2">Hồ sơ Công ty</div>
                    <button type="button" class="btn btn-primary rounded-pill w-50" data-bs-toggle="modal" data-bs-target="#introduce_detail">
                        Xem chi tiết
                    </button>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="fs-6 mb-4">
                    <span class="fw-semibold">Kính gửi Quý Khách hàng và Đối tác !</span>
                </div>
                <div class="fs-7">
                    <p><span class="fw-semibold">CÔNG TY CỔ PHẦN DATYSO VIỆT NAM</span>  tiền thân là Trung tâm Điện máy ĐTS, được thành lập từ năm 2011 tại Hà Nội. Người sáng lập là ông Phạm Văn Đạt và ông Nguyễn Bá Tỵ. </p>
                    <p>Trải qua 15 năm hình thành và phát triển, từ một trung tâm nhỏ với nhiệm vụ là sửa chữa và bảo dưỡng hệ thống máy cơ khí, Datyso đã không ngừng tiến bộ và phát triển vượt bậc, trở thành một trong những công ty hàng đầu chuyên cung cấp máy móc, giải pháp cơ khí với tên gọi mới: CÔNG TY CỔ PHẦN DATYSO VIỆT NAM. </p>
                    <p>Cấu trúc doanh nghiệp: DATYSO là tên gọi chính thức, có 2 trụ sở chính tại miền Nam và miền Bắc. </p>
                </div>
            </div>
        </div>
    </div>
    <div id="section2">
        <div class="text-center text-uppercase mb-2">
            <span class="border-bottom fw-bolder border-dark py-2 text-introduce text-dark-blue fs-3">SỨ MỆNH - TẦM NHÌN - GIÁ trị CỐT LÕI</span>
        </div>
        <div class="bg-new">
            <div class="container">
                <div class="row py-4">
                    <div class="col-sm-4 border-end">
                        <div class="">
                            <div class="text-uppercase text-dark-blue fw-bold text-introduce fs-3 mb-4">Sứ mệnh</div>
                            <div class="fs-7">
                                <p>Thông qua hoạt động kinh doanh để góp phần xây dựng đất nước Việt Nam ngày càng phát triển và nâng tầm vị thế quốc gia trên thế giới.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 border-end">
                        <div class="">
                            <div class="text-uppercase text-dark-blue fw-bold text-introduce fs-3 mb-4">Tầm nhìn</div>
                            <div class="fs-7">
                                <p>Tới năm 2030 trở thành tập đoàn kinh tế hàng đầu Việt Nam.</p>
                                <p>Phát triển phát triển hệ thống đại lý trên toàn quốc.</p>
                                <p>Phát triển nhà máy sản xuất quy mô lớn tập trung vào điều kiên chủ lực và thế mạnh của Việt Nam.</p>
                                <p>Phát triển hệ thống các công ty con trên toàn thế giới và mang sản phẩm tới tay khách hàng. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="fs-7">
                            <div class="text-uppercase text-dark-blue fw-bold text-introduce fs-3 mb-4">Giá trị cốt lõi</div>
                              <div class="fs-7">
                                <p> Trung thực tạo dựng niềm tin. </p>
                                <p> Uy tín là cam kết bền vững.</p>
                                <p> Cùng nhau phát triển cùng nhau vươn xa.</p>
                                <p> Làm việc bằng tâm, cống hiến bằng trách nhiệm.</p>
                                <p> Con người là tài sản quý giá nhất.</p>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="section3" class="container">
        <div class="text-center text-uppercase py-4">
            <span class="border-bottom fw-bolder border-dark py-2 text-introduce text-dark-blue fs-3">SLOGAN - ĐỊNH HƯỚNG PHÁT TRIỂN</span>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-3 bg-new py-4 rounded-start  img-introduce">
                        <img src="{{asset ('asset/images/image-removebg-preview.png ') }}" alt="" class="w-100">
                    </div>
                    <div class="col-sm-9 bg-new py-4 rounded-end">
                        <p class="text-dark-blue fs-5 fw-bold">Slogan “Nâng Tầm Giá Trị”</p>
                        <span class="fs-7">Datyso Việt Nam luôn hướng đến mang giá trị cho khách hàng vượt ra ngoài những gì khách hàng mong đợi. Khi khách hàng mua hàng ngoài giá trị sản phẩm khách hàng được nhận Datyso còn hướng đến nâng cấp dịch vụ, nâng cấp sản phẩm và hỗ trợ khách hàng để gia tăng giá trị cho khách hàng nhiều hơn</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-3 bg-new py-4 rounded-start  img-introduce">
                        <img src="{{asset ('asset/images/image-removebg-preview.png ') }}"  alt="" class="w-100">
                    </div>
                    <div class="col-sm-9 bg-new py-4 rounded-end">
                        <p class="text-dark-blue fs-5 fw-bold">Slogan “Nâng Tầm Giá Trị”</p>
                        <span class="fs-7">Datyso Việt Nam luôn hướng đến mang giá trị cho khách hàng vượt ra ngoài những gì khách hàng mong đợi. Khi khách hàng mua hàng ngoài giá trị sản phẩm khách hàng được nhận Datyso còn hướng đến nâng cấp dịch vụ, nâng cấp sản phẩm và hỗ trợ khách hàng để gia tăng giá trị cho khách hàng nhiều hơn</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-3 bg-new py-4 rounded-start  img-introduce">
                        <img src="{{asset ('asset/images/image-removebg-preview.png ') }}"  alt="" class="w-100">
                    </div>
                    <div class="col-sm-9 bg-new py-4 rounded-end">
                        <p class="text-dark-blue fs-5 fw-bold">Slogan “Nâng Tầm Giá Trị”</p>
                        <span class="fs-7">Datyso Việt Nam luôn hướng đến mang giá trị cho khách hàng vượt ra ngoài những gì khách hàng mong đợi. Khi khách hàng mua hàng ngoài giá trị sản phẩm khách hàng được nhận Datyso còn hướng đến nâng cấp dịch vụ, nâng cấp sản phẩm và hỗ trợ khách hàng để gia tăng giá trị cho khách hàng nhiều hơn</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-3 bg-new py-4 rounded-start  img-introduce">
                        <img src="{{asset ('asset/images/image-removebg-preview.png ') }}"  alt="" class="w-100">
                    </div>
                    <div class="col-sm-9 bg-new py-4 rounded-end">
                        <p class="text-dark-blue fs-5 fw-bold">Slogan “Nâng Tầm Giá Trị”</p>
                        <span class="fs-7">Datyso Việt Nam luôn hướng đến mang giá trị cho khách hàng vượt ra ngoài những gì khách hàng mong đợi. Khi khách hàng mua hàng ngoài giá trị sản phẩm khách hàng được nhận Datyso còn hướng đến nâng cấp dịch vụ, nâng cấp sản phẩm và hỗ trợ khách hàng để gia tăng giá trị cho khách hàng nhiều hơn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="section4" class="container">
        <div class="text-center text-uppercase py-4">
            <span class="border-bottom fw-bolder border-dark py-2 text-introduce text-dark-blue fs-3">ĐỐI TÁC & KHÁCH HÀNG</span>
        </div>
        <div class="row mb-4">
            <div class="col-sm-4">
                <img src="{{ asset('asset/images/sonha.png') }}" class="w-100 object-fit-cover" style="height: 238px;" alt="">
            </div>
            <div class="col-sm-4">
                <img src="{{ asset('asset/images/teraco.png') }}" class="w-100 object-fit-cover" style="height: 238px;" alt="">
            </div>
            <div class="col-sm-4">
                <img src="{{ asset('asset/images/hoa-phat.png') }}" class="w-100 object-fit-cover" style="height: 238px;" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <img src="{{ asset('asset/images/anthinh.png') }}" class="w-100 object-fit-cover" style="height: 418px;"  alt="">
            </div>
            <div class="col-sm-4">
                <img src="{{ asset('asset/images/coma26.jfif') }}" class="w-100 object-fit-cover" style="height: 418px;"  alt="">
            </div>
            <div class="col-sm-4">
                <img src="{{ asset('asset/images/rahy.png') }}" class="w-100 object-fit-cover" style="height: 418px;"  alt="">
            </div>
        </div>
        
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hội Đồng Quản Trị</title>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
            <style>
                .board-title {
                    background-color: blue;
                    color: white;
                    font-family: 'Roboto', sans-serif;
                    font-weight: bold;
                    font-size: 20px;
                    padding: 10px 20px;
                    display: inline-block;
                    text-align: center;
                    text-transform: uppercase;
                }

                .directors {
                    display: flex;
                    justify-content: center;
                    gap: 20px;
                    margin-top: 20px;
                }

                .director {
                    text-align: center;
                    color: black;
                }

                .director img {
                    width: 350px;
                    height: auto;
                }

                .director-name {
                    font-weight: bold;
                    margin-top: 10px;
                    color: black;
                    font-size: 18px;
                    /* cỡ chữ cho tên giám đốc */
                }

                .director-title {
                    color: black;
                    font-size: 16px;
                    /* cỡ chữ cho chức danh giám đốc */
                }
            </style>
        </head>

        <body>
            <div class="text-center text-uppercase py-4">
                <div class="py-2 text-introduce text-dark-blue fs-3 mb-4">
                    <span class="border-bottom fw-bolder">BAN LÃNH ĐẠO</span>
                    <div class="text-center text-uppercase py-4">
                        <div class="py-2 fs-3 mb-4 board-title">
                            HỘI ĐỒNG QUẢN TRỊ DATYSO
                        </div>
                        <div class="directors">
                            <div class="director">
                                <img src="asset/images/s1.jpg" alt="Giám Đốc 1">
                                <div class="director-name">Phạm Văn Đạt</div>
                                <div class="director-title">Chủ tịch HĐQT</div>
                            </div>
                            <div class="director">
                                <img src="asset/images/s2.jpg" alt="Giám Đốc 2">
                                <div class="director-name">Nguyễn Bá Tỵ</div>
                                <div class="director-title">Phó chủ tịch HĐQT</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>

        <div class="text-center text-uppercase py-4">
            <div class="py-2 text-introduce text-dark-blue fs-3 mb-4"><span class="border-bottom fw-bolder">HOẠT ĐỘNG CỦA DATYSO</span></div>
        </div>
    </div>
    
    <div class="container">
    <div class="row mb-4">
        <div class="col-sm-4">
            <img src="asset/images/hd1.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 1">
        </div>
        <div class="col-sm-4">
            <img src="asset/images/hd2.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 2">
        </div>
        <div class="col-sm-4">
            <img src="asset/images/hd3.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 3">
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-sm-4">
            <img src="asset/images/hd4.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 4">
        </div>
        <div class="col-sm-4">
            <img src="asset/images/hd5.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 5">
        </div>
        <div class="col-sm-4">
            <img src="asset/images/hd6.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 6">
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-sm-4">
            <img src="asset/images/hd7.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 7">
        </div>
        <div class="col-sm-4">
            <img src="asset/images/hd8.JPG" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 8">
        </div>
        <div class="col-sm-4">
            <img src="asset/images/hd9.jpg" class="w-100 object-fit-cover" style="height: 200px;" alt="Activity 9">
        </div>
    </div>
</div>


</div>
<div class="modal fade modal-xl" id="introduce_detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Hồ sơ công ty</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @include('user.introduce.catalog')
        </div>
    </div>
    </div>
</div>
@endsection
@section('footer')
@endsection
