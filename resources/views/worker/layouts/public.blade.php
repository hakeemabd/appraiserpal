<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ $title or 'Appraisers solutions' }}</title>

    <!-- CSS  -->
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.4/css/materialize.min.css">
    <link href="/styles/worker.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body class="public-page">
<nav class="white" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="{{ route('worker:landing') }}" class="brand-logo">Appraisers Solutions</a>
        <ul class="right hide-on-med-and-down">
            @include('widgets.menu')
        </ul>

        <ul id="nav-mobile" class="side-nav">
            @include('widgets.menu')
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
</nav>

@yield('content')

<footer class="page-footer teal">
    <div class="container">
        <div class="row">
            <div class="col l3 s12">
                <h5 class="white-text">Contacts</h5>
                <ul>
                    <li><a class="white-text" href="{{ route('worker:landing') }}">info@appraiserssolutions.com</a></li>
                    <li><a class="white-text" href="mailto:{{ $contactEmail or '' }}">{{ $contactEmail or '' }}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            © 2017 <a class="grey-text text-lighten-3" href="{{ route('worker:landing') }}">APPRAISERS SOLUTIONS</a>
        </div>
    </div>
</footer>

<!--  Scripts-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.4/js/materialize.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.js"></script>

<!--  Scripts-->
<script type="text/javascript" src="{{ elixir('worker/js/all.js') }}"></script>
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
@stack('scripts')
</body>
</html>
