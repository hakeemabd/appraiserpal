<div id="{{$id}}">
    <div class="swiper-container object-slider">
        <div class="swiper-wrapper">
            @foreach ($slides as $slide)
                <div class="swiper-slide">
                    <img class="img-sim" data-img="{{ $slide->getImageUrl(App\Models\Attachment::IMG_SOURCE) }}"
                         src = '{{ $slide->getImageUrl(App\Models\Attachment::IMG_SOURCE) }}')>
                    <h5 class="h5">{{$slide->formatLabel()}}</h5>
                </div>
            @endforeach
        </div>
    </div>
    <div class="swiper-button-next swiper-button-black"></div>
    <div class="swiper-button-prev swiper-button-black"></div>
</div>