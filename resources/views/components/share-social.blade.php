@props(['model', 'shareLink'])

@php
    // $shareLink = urlencode(route('post.detail', ['postSlug' => $post->slug]));
    $shareTitle = urlencode($model->title ?? 'Chia sẻ mạng xã hội');
    $shareImage = urlencode($model->image ?? asset('images/default-share.jpg'));
@endphp

<div class="social-icons share-icons share-row relative">
    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer.php?u={{ $shareLink }}"
    class="icon primary button circle tooltip facebook"
    aria-label="Chia sẻ lên Facebook"
    rel="noopener nofollow"
    target="_blank"
    onclick="window.open(this.href,this.title,'width=500,height=500,top=300,left=300'); return false;">
        <svg viewBox="0 0 320 512" width="18" height="18" fill="currentColor">
            <path d="M279.14 288l14.22-92.66h-88.91V127.9c0-25.35 12.42-50.06 
            52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 
            44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
        </svg>
    </a>

    <!-- Twitter -->
    <a href="https://twitter.com/share?url={{ $shareLink }}&text={{ $shareTitle }}"
    class="icon primary button circle tooltip twitter"
    aria-label="Chia sẻ lên Twitter"
    rel="noopener nofollow"
    target="_blank"
    onclick="window.open(this.href,this.title,'width=500,height=500,top=300,left=300'); return false;">
        <svg viewBox="0 0 512 512" width="18" height="18" fill="currentColor">
            <path d="M459.37 151.716c.325 4.548.325 9.097.325 
            13.645 0 138.72-105.583 298.558-298.558 
            298.558-59.452 0-114.68-17.219-161.137-47.106 
            8.447.974 16.568 1.299 25.34 1.299 
            49.055 0 94.213-16.568 130.274-44.832-46.132-.974-84.792-31.188-98.112-72.772 
            6.498.974 12.995 1.624 19.818 
            1.624 9.421 0 18.843-1.299 27.614-3.573
            -48.081-9.747-84.143-51.98-84.143-102.985v-1.299
            c13.969 7.797 30.214 12.67 47.431 
            13.319-28.264-18.843-46.781-51.005-46.781-87.391
            0-19.492 5.197-37.36 14.294-52.954 
            51.655 63.675 129.3 105.258 216.365 
            109.807-1.624-7.797-2.599-15.919-2.599-24.041 
            0-57.828 46.782-104.934 104.934-104.934 
            30.213 0 57.502 12.67 76.67 33.137 
            23.715-4.548 46.456-13.32 66.599-25.34
            -7.798 24.366-24.366 44.833-46.132 57.827
            21.117-2.273 41.584-8.122 60.426-16.243
            -14.292 20.791-32.161 39.308-52.628 54.253z"/>
        </svg>
    </a>

    <!-- Pinterest -->
    <a href="https://pinterest.com/pin/create/button?url={{ $shareLink }}&media={{ $shareImage }}&description={{ $shareTitle }}"
    class="icon primary button circle tooltip pinterest"
    aria-label="Lưu lên Pinterest"
    rel="noopener nofollow"
    target="_blank"
    onclick="window.open(this.href,this.title,'width=500,height=500,top=300,left=300'); return false;">
        <svg viewBox="0 0 496 512" width="18" height="18" fill="currentColor">
            <path d="M496 256c0 137-111 248-248 248-28.5 0-55.8-4.8-81.2-13.7 
            11.2-17.9 28.1-49.7 34.4-74.1 3.3-12.6 
            17-64.3 17-64.3 8.4 16 33 29.5 59.1 29.5 
            77.7 0 133.6-71.4 133.6-160.1 0-85-72.4-148.7-164.9-148.7
            -115.5 0-176.6 77.6-176.6 162.3 
            0 39.4 21.1 88.5 54.7 104.1 
            5.1 2.4 7.8 1.3 9-.3 0 0 1.2-4.9 1.8-7.6 
            .9-3.7 5.4-21.8 7.5-30.2 .7-2.8 .4-5.2-1.9-7.9 
            -11-13.4-20-38.1-20-61.2 0-59.3 44.5-116.6 
            120.3-116.6 65.5 0 111.7 45.6 111.7 110.3 
            0 68.4-34.4 115.9-79.1 115.9 
            -24.7 0-43.1-20.4-37.2-45.4 
            7.1-29.9 21-62.2 21-83.8 
            0-19.3-10.4-35.4-31.9-35.4 
            -25.3 0-45.6 26.2-45.6 61.3 
            0 22.3 7.5 37.4 7.5 37.4s-24.9 105.7-29.2 124.2c-4.9 
            21.3-2.8 51.6-.8 71.2C67.1 456 0 365.3 0 256 
            0 119 111 8 248 8s248 111 248 248z"/>
        </svg>
    </a>

    <!-- LinkedIn -->
    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareLink }}&title={{ $shareTitle }}"
    class="icon primary button circle tooltip linkedin"
    aria-label="Chia sẻ lên LinkedIn"
    rel="noopener nofollow"
    target="_blank"
    onclick="window.open(this.href,this.title,'width=500,height=500,top=300,left=300'); return false;">
        <svg viewBox="0 0 448 512" width="18" height="18" fill="currentColor">
            <path d="M100.28 448H7.4V148.9h92.88zm-46.44-338C24.3 
            110 0 85.7 0 56.4a56.44 56.44 0 01112.88 0c0 
            29.3-24.3 53.6-59.04 53.6zM447.9 
            448h-92.68V302.4c0-34.7-.7-79.3-48.3-79.3-48.3 
            0-55.7 37.7-55.7 76.7V448h-92.68V148.9h89V196h1.3c12.4-23.4 
            42.7-48.3 87.8-48.3 93.8 0 111.2 61.8 111.2 
            142.3V448z"/>
        </svg>
    </a>

    <!-- Tumblr -->
    <a href="https://tumblr.com/widgets/share/tool?canonicalUrl={{ $shareLink }}"
    class="icon primary button circle tooltip tumblr"
    aria-label="Chia sẻ lên Tumblr"
    rel="noopener nofollow"
    target="_blank"
    onclick="window.open(this.href,this.title,'width=500,height=500,top=300,left=300'); return false;">
        <svg viewBox="0 0 320 512" width="18" height="18" fill="currentColor">
            <path d="M309.8 480.3c-12.5 5.9-36.6 
            11.1-54.3 11.5-53.8 1.1-64.2-39.5-64.2-69.2V223.9h132.5V152H191.3V0h-75.5c-1.2 
            0-3.3 1.1-3.6 3.9-4.3 39.5-22.8 108.6-99.2 
            136.3v83.7h51.1v208.3c0 56.5 41.7 136.6 
            152 135.7 37.1-.3 78.4-16.2 87.7-29.6z"/>
        </svg>
    </a>
</div>