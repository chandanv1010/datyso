
@props(['gallery', 'product'])

@php
function convertYoutube($url) {
    // Nếu là link YouTube bình thường
    if (preg_match('/(?:youtube\.com|youtu\.be)/', $url)) {
        // Trích xuất video id
        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $match);
        $videoId = $match[1] ?? null;

        // Trích xuất playlist nếu có
        preg_match('/[?&]list=([a-zA-Z0-9_-]+)/', $url, $listMatch);
        $listId = $listMatch[1] ?? null;

        if ($videoId) {
            $embed = "https://www.youtube.com/embed/{$videoId}";
            if ($listId) $embed .= "?list={$listId}";
            return $embed;
        }
    }
    // Không hợp lệ thì trả nguyên
    return $url;
}
$videoUrl = isset($product->video) ? convertYoutube($product->video) : null;
@endphp

@if(isset($gallery) && !empty($gallery) && !is_null($gallery))
    <div class="product-gallery">
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-container">
            <div class="swiper-wrapper big-pic">
                @if(isset($product->video) && !is_null($product->video) && !empty($product->video))
                <div class="swiper-slide" data-swiper-autoplay="2000">
                    <div class="slide-video">
                        
                        <iframe width="100%" height="315"
                                src="{{ $videoUrl }}"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
                @endif
                <?php foreach($gallery as $key => $val){  ?>
                    <div class="swiper-slide" data-swiper-autoplay="2000">
                        <a href="{{ $val }}" data-fancybox="my-group" class="image img-cover">
                            <img src="{{ $val }}" alt="<?php echo $val ?>">
                        </a>
                    </div>
                <?php }  ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="swiper-container-thumbs">
            <div class="swiper-wrapper pic-list">
                @if(isset($product->video) && !is_null($product->video) && !empty($product->video))
                <div class="swiper-slide">
                    <span  class="image img-scaledown"><img src="{{  asset('asset/video.png') }}"></span>
                </div>
                @endif
                <?php foreach($gallery as $key => $val){  ?>
                <div class="swiper-slide">
                    <span  class="image img-cover"><img src="{{  $val }}" alt="<?php echo $val ?>"></span>
                </div>
                <?php }  ?>
            </div>
        </div>
    </div>
@endif