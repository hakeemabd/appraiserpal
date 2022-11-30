<!--hero section-->

<header class="hero-section">
    @include('customer.landing.headerNav')
    @if(!isset($page))
        @include('customer.landing.welcome')
    @endif
</header>

<!--hero section end-->