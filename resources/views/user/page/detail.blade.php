@extends('user.main')
@section('templateContent')
@php
    $daysOfWeek = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
@endphp
<div id="detail-section" class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" style="text-decoration: none; font-weight: 500;">Trang Chủ</a></li>
          <li class="breadcrumb-item"><a href="{{ route('page.index') }}" style="text-decoration: none; font-weight: 500;">Tin Tức</a></li>
          <li class="breadcrumb-item" aria-current="page" class=""> {{ $itemDetail->name}} </li>
        </ol>
    </nav>

    <div>
        <div class="row">
            <!-- Detail  -->
            <div class="col-9">
                <div class="py-2">
                    <h5 style="font-size: 1.5rem; font-weight: 600;"> {{ $itemDetail->name }}</h5>
                    <div class="d-flex py-2 pe-4" style="border-top: 0.8px solid rgba(128, 128, 128, 0.1); border-bottom: 0.8px solid rgba(128, 128, 128, 0.1);">
                        <div>
                            <span class="text-secondary" style="font-size: 0.8rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"></path>
                                </svg>
                                {{ $daysOfWeek[$itemDetail->updated_at->dayOfWeek] }} -  {{ $itemDetail->updated_at->format('d/m/Y - H:i') }}
                            </span>
                            <span class="text-secondary mx-3" style="font-size: 0.8rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em" fill="currentColor" class="bi bi-eye mb-1" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                  </svg>
                                  {{ $itemDetail->view_count }} Lượt xem
                            </span>
                        </div>
                        <div class="ms-auto">
                            <span class="text-secondary mx-3" style="font-size: 0.8rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em" fill="currentColor" class="bi bi-hand-thumbs-up mb-1" viewBox="0 0 16 16">
                                    <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
                                </svg>
                                Thích
                            </span>
                            <span class="text-secondary" style="font-size: 0.8rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="0.9em" height="0.9em" fill="currentColor" class="bi bi-share mb-1" viewBox="0 0 16 16">
                                    <path d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5m-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3"/>
                                </svg>
                                Chia sẻ
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <p style="font-weight: 500;">
                            {{ $itemDetail->slug }}
                        </p>
                        <div class="my-4 px-5 editor-content">
                   <p>
                   {!! $itemDetail->content !!}
                   </p>
                  </div>

                    </div>
                </div>

                <!-- Related News -->
                <div>
                    <div style="border-bottom: 2px solid #002ab0; ">
                        <h6 class="d-inline text-white pb-1 px-2 pt-1" style="background-color: #002ab0;">TIN LIÊN QUAN</h6>
                    </div>
                    
                    <div class="row my-3">
                        @foreach ( $forItems as $forItem)
                            <div class="col-4">
                                <div class="bg-white px-2 py-3 shadow">
                                    <div>
                                        <img src="{{ $forItem->image}}" alt="" style="width: 100%;">
                                    </div>
                                    <div class="mt-2">
                                        <h6 style="font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">[INFOGRAPHIC]{{ $forItem->name}}</h6>
                                    </div>
                                    <span style="color: grey; font-size: 0.8rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="0.8em" height="0.8em" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16">
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"></path>
                                        </svg>
                                        {{ $daysOfWeek[$forItem->updated_at->dayOfWeek] }} -  {{ $forItem->updated_at->format('d/m/Y - H:i') }}
                                    </span>
                                </div>
                            </div>
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
                            <div class="shadow my-3" style="position: relative;">
                                <img src="{{ $thearItem->image}}" alt="" style="width: 100%; height: 8rem;">
                                <div style="background-color: rgba(0, 0, 0, 0.4); width: 100%; height: 100%; position: absolute; top: 0;"></div>
                                <div>
                                    <h6 class="text-white" style="position: absolute; top: 5rem; left: 1rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">{{ $thearItem->name}} </h6>
                                    <span class="text-white" style="font-size: 0.8rem; position: absolute; top: 6.7rem; left: 1rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16" >
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                        </svg>
                                        {{ $thearItem->updated_at->format('d/m/Y - H:i') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Most View Tin Tuc -->
                <div id="most-view-news">
                    <h6 style="color: #002ab0; border-bottom: 1px solid #002ab0;">TIN XEM NHIỀU NHẤT</h6>
                    <div>
                        @foreach($thearItemsPages as $thearItemsPage) 
                            <div class="shadow my-3" style="position: relative;">
                                <img src="{{ $thearItemsPage->image}}" alt="" style="width: 100%; height: 8rem;">
                                <div style="background-color: rgba(0, 0, 0, 0.4); width: 100%; height: 100%; position: absolute; top: 0;"></div>
                                <div>
                                    <h6 class="text-white" style="position: absolute; top: 5rem; left: 1rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; ">{{ $thearItemsPage->name}} </h6>
                                    <span class="text-white" style="font-size: 0.8rem; position: absolute; top: 6.7rem; left: 1rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-calendar mb-1" viewBox="0 0 16 16" >
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                        </svg>
                                        {{ $thearItemsPage->updated_at->format('d/m/Y - H:i') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        
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
