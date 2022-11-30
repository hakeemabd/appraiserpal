<!DOCTYPE html>
<html>
<head>
    <title>Appraiser Solutions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="/styles/worker.css" rel="stylesheet">
    @yield('custom-styles')
</head>
<body>
{{-- Nav --}}
<nav class="teal darken-1 main-nav">
    <div class="nav-wrapper container-custom">
        <a href="{{ route('worker:dashboard') }}" class="brand-logo waves-effect">Appraiser Solutions</a>
        <a href="#" data-activates="mobile-menu" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li><a href='{{ route('customer:dashboard') }}' class='waves-effect'>Dashboard</a></li>
            <li><a href='{{ route('customer:messages') }}' class='waves-effect'>Messages</a></li>
            {{-- <li><a href='#!' class='waves-effect'>Payments</a></li> --}}
            <li><a href='{{ route('customer:logout') }}'
                   class='waves-effect'><strong>{{ \Cartalyst\Sentinel\Laravel\Facades\Sentinel::check()->fullName }}</strong>
                    [Log out]</a></li>
        </ul>
        <ul class="side-nav" id="mobile-menu" style="left: 0;">
            <li><a href='{{ route('customer:dashboard') }}' class='waves-effect'>Dashboard</a></li>
            {{-- <li><a href='#!' class='waves-effect'>Orders</a></li> --}}
            <li><a href='customer:messages' class='waves-effect'>Messages</a></li>
            {{-- <li><a href='#!' class='waves-effect'>Payments</a></li> --}}
            <li><a href='{{ route('customer:logout') }}' class='waves-effect'>{{ Sentinel::check()->fullName }} Log
                    out</a></li>
        </ul>
    </div>
</nav>

<main class="container-custom">
    <div class="row">
        @if(!isset($breadcumb) || (isset($breadcumb) && $breadcumb == true))
            <div class="col s12 card-panel grid-panel">
                <nav class="teal lighten-2">
                    <div class="nav-wrapper">
                        <div class="col s12 breadcrumb-wrap">
                            @yield('breadcrumb')
                        </div>
                    </div>
                </nav>
                <div class="divider"></div>

                @yield('content')
            </div>
        @else
            @yield('content')
        @endif
    </div>
</main>

<script type="text/javascript">
    var __MSG = {};
    //###formatter:off
    @if (Session::has('__msg'))
        __MSG = {!! json_encode(Session::pull('__msg')) !!};
    @endif
    //###formatter:on
</script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="{{ asset('build/scripts/vendor.js') }}"></script>
<script src="{{ elixir('worker/js/all.js') }}"></script>
{{-- Scripts --}}
@stack('scripts')
<!--Start of Zendesk Chat Script-->
<script type="text/javascript">
    $.fn.dataTable.ext.errMode = 'throw';
    window.$zopim || (function (d, s) {
        var z = $zopim = function (c) {
            z._.push(c)
        }, $ = z.s =
            d.createElement(s), e = d.getElementsByTagName(s)[0];
        z.set = function (o) {
            z.set._.push(o)
        };
        z._ = [];
        z.set._ = [];
        $.async = !0;
        $.setAttribute('charset', 'utf-8');
        $.src = 'https://v2.zopim.com/?4RBW4bgX3I2xLetyaI4xjohKKc7JZtNl';
        z.t = +new Date;
        $.type = 'text/javascript';
        e.parentNode.insertBefore($, e)
    })(document, 'script');
</script>
<!--End of Zendesk Chat Script-->
</body>
</html>
