<!--hero section-->

<header class="hero-section">
    @include('landing.headerNav')
    @if(!isset($page))
        @include('landing.welcome')
    @endif
</header>

<!--hero section end-->