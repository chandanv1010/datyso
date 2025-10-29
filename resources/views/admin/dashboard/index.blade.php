@section('headerTitle', 'Quản trị - Trang chủ')
@extends('admin.layout')
@section('headerStyle')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('templateContent')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none">Trang chủ </a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
<section class="content">
    <div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-4 col-6 d-flex">
        <!-- small box -->
        <div class="small-box bg-info flex-fill">
            <div class="inner">
            <h3>{{$data['TotalViewsCount']}}</h3>

            <p class="mt-4">Số lượng khách truy cập website</p>
            </div>
            <div class="icon">
            <i class="ion ion-bag"></i>
            </div>
        </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
            <h3>{{ $data['percentageContactBeenPressed'] }}<sup style="font-size: 20px">%</sup></h3>

            <p class="mt-4">Tỷ lệ bấm vào nút liên hệ so với tổng lượt xem sản phẩm</p>
            </div>
            <div class="icon">
            <i class="ion ion-stats-bars"></i>
            </div>
        </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
            <h3>{{ $data['newVisitorRate'] }}<sup style="font-size: 20px">%</sup></h3>
            <p class="mt-4">Tỷ lệ khách truy cập mới so với khách truy cập quay lại</p>
            </div>
            <div class="icon">
            <i class="ion ion-person-add"></i>
            </div>
        </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-lg-6 col-6">
        <h4 class="text-center font-weight-bold">Top 5 Tin tức được người dùng truy cập nhiều nhất</h4>
        <div><canvas id="top5PostViews"></canvas></div>
        </div>
        <div class="col-lg-6 col-6">
        <h4 class="text-center font-weight-bold">Top 10 Sản phẩm được xem nhiều nhất</h4>
        <div><canvas id="top10ProductViews"></canvas></div>
        </div>
    </div>
    </div><!-- /.container-fluid -->

    <script>
    var ctx = document.getElementById('top5PostViews').getContext('2d');
    var top5PostViews = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($data['top5PostViewsLabel']) !!},
            datasets: [{
                label: 'Lượt truy cập',
                data: {!! json_encode($data['top5PostViewsData']) !!},
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                ],
                borderWidth: 1
            }]
        },
            options: {
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
    });

    var ctx1 = document.getElementById('top10ProductViews').getContext('2d');
    var top10ProductViews = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: {!! json_encode($data['top10ProductViewsLabel']) !!},
            datasets: [{
                label: 'Lượt xem',
                data: {!! json_encode($data['top10ProductViewsData']) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                ],
                borderWidth: 1
            }]
        },
            options: {
                scales: {
                    x:{
                        display: false
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
    });
</script>
</section>
@endsection
