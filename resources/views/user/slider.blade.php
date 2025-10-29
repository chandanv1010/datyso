<div class="slide-image">
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($sliders as $slider)
                <div class="item carousel-item active">
                    <img style="object-fit: cover; height: 100%;width: 100%;"
                        id="blah" src="{{ isset($slider) ? $slider->image_url : '' }}" alt="slide image" />
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: #0d6efd"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: #0d6efd"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
