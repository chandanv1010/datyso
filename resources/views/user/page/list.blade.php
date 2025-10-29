@extends('user.main')
@section('templateContent')
@php
    $daysOfWeek = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
@endphp
<div id="event-section" class="container mt-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#" class="no-active">Tin tức</a></li>
        </ol>
    </nav>
    <div>
        <div class="mt-3">
            <div class="row">
                <!-- Tin Tuc -->
                <div class="col-9">
                    <div class="news-img" style="border-bottom: 2px solid #002ab0; ">
                        <h6 class="d-inline text-white pb-1 px-2 pt-1" style="background-color: #002ab0;">TIN TỨC</h6>
                    </div>

                    <div class="mt-3">
                        <!-- Main News -->
                        @foreach ($items as $item)
                            <a href="{{ route('page.detail', ['id' => $item->id]) }}" class="d-flex" style="text-decoration: none;">
                                <div class="news-img flex-shrink-0">
                                    <img class="main-news-img" src="{{ $item->image }}" alt="" style="width: 100%; height: 12rem;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #002ab0; font-weight: 600;">{{ $item->name }}</h5>
                                    <span class="text-secondary" style="font-size: 0.8rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="0.8em" height="0.8em" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                        </svg>
                                        {{ $daysOfWeek[$item->updated_at->dayOfWeek] }} -  {{ $item->updated_at->format('d/m/Y - H:i') }}
                                    </span>
                                    <p style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: black;">
                                        {{ $item->slug }}
                                    </p>
                                    <button class="py-2 px-3" id="main-news-read-more-btn" >Đọc thêm</button>
                                </div>
                            </a>
                        @endforeach
                         <!-- Sub News -->
                        <div class="row mt-4">
                            @foreach($forItems as $forItem)
                                <a href="{{ route('huong-dan.detail', ['id' => $forItem->id]) }}" style="text-decoration: none; color: black;">
                                    <div class="col-6 mb-4">
                                        <div class="d-flex">
                                            <div class="news-img flex-shrink-0">
                                                <img src="{{ $forItem->image }}" alt="" style="width: 10rem; height: 5rem;">
                                            </div>
                                            <div class="flex-grow-1 ps-2">
                                                <h6 class="mb-0" style="font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $forItem->name }}</h6>
                                                <span style="color: grey; font-size: 0.8rem;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="0.8em" height="0.8em" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                                    </svg>
                                                    {{ $daysOfWeek[$item->updated_at->dayOfWeek] }} -  {{ $item->updated_at->format('d/m/Y - H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div> 
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Side Tin Tuc -->
                <div class="col-3">
                    <!-- Latest Tin Tuc -->
                    <div id="latest-news" class="mt-2">
                        <h6 style="color: #002ab0; border-bottom: 1px solid #002ab0;">TIN TỨC MỚI</h6>
                        <div>
                            @foreach($thearItems as $thearItem)
                                <a href="{{ route('page.detail', ['id' => $thearItem->id]) }}" style="text-decoration: none; color: black;">
                                    <div class="shadow my-3" style="position: relative;">
                                        <img src="{{ $thearItem->image}}" alt="" style="width: 100%; height: 8rem;">
                                        <div style="background-color: rgba(0, 0, 0, 0.4); width: 100%; height: 100%; position: absolute; top: 0;"></div>
                                        <div>
                                            <h6 class="text-white" style="position: absolute; top: 5rem; left: 1rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; ">{{ $thearItem->name}} </h6>
                                            <span class="text-white" style="font-size: 0.8rem; position: absolute; top: 6.7rem; left: 1rem">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16" >
                                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                                </svg>
                                                {{ $thearItem->updated_at->format('d/m/Y - H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Most View Tin Tuc -->
                    <div id="most-view-news">
                        <h6 style="color: #002ab0; border-bottom: 1px solid #002ab0;">TIN XEM NHIỀU NHẤT</h6>
                        <div>
                            @foreach($thearItemsPages as $thearItemsPage)
                                <a href="{{ route('huong-dan.detail', ['id' => $thearItemsPage->id]) }}" style="text-decoration: none; color: black;">
                                    <div class="shadow my-3" style="position: relative;">
                                        <img src="{{ $thearItemsPage->image}}" alt="" style="width: 100%; height: 8rem;">
                                        <div style="background-color: rgba(0, 0, 0, 0.4); width: 100%; height: 100%; position: absolute; top: 0;"></div>
                                        <div>
                                            <h6 class="text-white" style="position: absolute; top: 5rem; left: 1rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">{{ $thearItemsPage->name}} </h6>
                                            <span class="text-white" style="font-size: 0.8rem; position: absolute; top: 6.7rem; left: 1rem">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16" >
                                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                                </svg>
                                                {{ $thearItemsPage->updated_at->format('d/m/Y - H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer')
@endsection
@include('user.buttonSocial')
