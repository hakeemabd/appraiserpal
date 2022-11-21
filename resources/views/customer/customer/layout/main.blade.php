<!DOCTYPE html>
<html>
<head>
    <title>{{ $pageTitle or "Appraiser Pal INC" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/jquery.min.js" class="__web-inspector-hide-shortcut__"></script>
    <script src="js/bootstrap.min.js" class="__web-inspector-hide-shortcut__"></script>

    <link href="/styles/customer.css" rel="stylesheet">

    @stack('styles')
</head>
<body>
@yield('header')
@yield('content')
@stack('angular-scripts')
<script type="text/javascript">
    var __MSG = {};
    //###formatter:off
    @if (Session::has('__msg'))
       __MSG = {!! json_encode(Session::pull('__msg')) !!};
    @endif
    //###formatter:on
</script>
<script type="text/javascript" src="{{ elixir('customer/js/all.js') }}"></script>
@stack('scripts')
</body>
<!--Start of Zendesk Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){
z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='https://v2.zopim.com/?4RBW4bgX3I2xLetyaI4xjohKKc7JZtNl';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
<!--End of Zendesk Chat Script-->
</html>
