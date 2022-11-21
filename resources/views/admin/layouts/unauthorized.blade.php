<!DOCTYPE html>
<html lang="en">
<head>
    <title>Appraiser Pal Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS  -->
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.4/css/materialize.min.css" type="text/css"
          rel="stylesheet" media="screen,projection"/>
</head>
<body>
<nav class="teal" role="navigation">
    <div class="nav-wrapper container">
        <a href="{{ route('admin:dashboard') }}" class="brand-logo waves-effect">Appraiser Pal</a>
    </div>
</nav>
<div class="section no-pad-bot">
    <div class="container">
        @yield('content')
    </div>
</div>


<!--  Scripts-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.4/js/materialize.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.js"></script>

<script type="text/javascript">
    var __MSG = {};
    //###formatter:off
    @if (Session::has('__msg'))
       __MSG = {!! json_encode(Session::pull('__msg')) !!};
    @endif
    //###formatter:on
</script>

{{-- Main script --}}
<script src="{{ elixir('admin/js/all.js') }}"></script>

{{-- Scripts --}}
@stack('scripts')
</body>
</html>