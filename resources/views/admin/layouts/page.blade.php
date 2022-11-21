<!DOCTYPE html>
<html>
<head>
    <title>Appraiser Pal Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="/styles/admin.css" rel="stylesheet">
    @yield('custom-styles')
    <style>
        .datagrid tr th, .datagrid tr td {
            display: table-cell !important;
        }

        #DataTables_Table_0_wrapper {
            overflow-y: auto !important;
        }
    </style>
</head>
<body>
{{-- Nav --}}
<nav class="teal darken-1 main-nav">
    <div class="nav-wrapper">
        <a href="{{ route('admin:dashboard') }}" class="brand-logo waves-effect">Appraiser Pal</a>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>

        <ul id="user-dropdown" class="dropdown-content">
            <li><a href="{{ route('admin:usersList', ['role' => 'customer']) }}">Customers</a></li>
            <li><a href="{{ route('admin:usersList', ['role' => 'worker']) }}">Workers</a></li>
            <li><a href="{{ route('admin:workerGroup.index') }}">Worker groups</a></li>
            <li><a href="{{ route('admin:usersList', ['role' => 'administrator']) }}">Admins</a></li>
            @if(\Sentinel::check()->inRole('administrator'))
                <li><a href="{{ route('admin:usersList', ['role' => 'sub-admin']) }}">Sub Admins</a></li>
            @endif
        </ul>
        <ul id="content-dropdown" class="dropdown-content">
            <li><a href="{{ route('admin:pages') }}">Pages</a></li>
            <li><a href="#!">Sections</a></li>
            <li><a href="#!">Emails</a></li>
            <li><a href="#!">Reviews</a></li>
            {{--<li><a href="#!">FAQ</a></li>--}}
        </ul>
        <ul id="order-dropdown" class="dropdown-content">
            <li><a href="{{ route('admin:order.index') }}">Order tracking</a></li>
            <li><a href="{{ route('admin:reportType.index') }}">Order Category</a></li>
        </ul>
        @if(\Sentinel::check()->inRole('administrator'))
            <ul id="payment-dropdown" class="dropdown-content">
                <li><a href="{{ route('admin:transactions.customer') }}">Customer transactions</a></li>
                <li><a href="{{ route('admin:payments.due') }}">Payments due</a></li>
                <li><a href="{{ route('admin:payments.complete') }}">Payments complete</a></li>
                <li><a href="{{ route('admin:promoCode.index') }}">Promo codes</a></li>
            </ul>
        @endif
        <ul id="user-dropdown2" class="dropdown-content">
            <li><a href="{{ route('admin:usersList', ['role' => 'customer']) }}">Customers</a></li>
            <li><a href="{{ route('admin:usersList', ['role' => 'worker']) }}">Workers</a></li>
            <li><a href="{{ route('admin:workerGroup.index') }}">Worker groups</a></li>
            <li><a href="{{ route('admin:usersList', ['role' => 'administrator']) }}">Admins</a></li>
            @if(\Sentinel::check()->inRole('administrator'))
                <li><a href="{{ route('admin:usersList', ['role' => 'sub-admin']) }}">Sub Admins</a></li>
            @endif
        </ul>
        <ul id="content-dropdown2" class="dropdown-content">
            <li><a href="{{ route('admin:pages') }}">Pages</a></li>
            <li><a href="#!">Sections</a></li>
            <li><a href="#!">Emails</a></li>
            <li><a href="#!">Reviews</a></li>
            {{--<li><a href="#!">FAQ</a></li>--}}
        </ul>
        <ul id="order-dropdown2" class="dropdown-content">
            <li><a href="{{ route('admin:order.index') }}">Order tracking</a></li>
            <li><a href="{{ route('admin:reportType.index') }}">Order Category</a></li>
        </ul>
        @if(\Sentinel::check()->inRole('administrator'))
            <ul id="payment-dropdown2" class="dropdown-content">
                <li><a href="{{ route('admin:transactions.customer') }}">Customer transactions</a></li>
                <li><a href="{{ route('admin:payments.due') }}">Payments due</a></li>
                <li><a href="{{ route('admin:payments.complete') }}">Payments complete</a></li>
                <li><a href="{{ route('admin:promoCode.index') }}">Promo codes</a></li>
            </ul>
        @endif
        <ul class="right hide-on-med-and-down">
            <li><a href='{{ route('admin:dashboard') }}' class='waves-effect'>Dashboard</a></li>
            <li><a class="dropdown-button" href="#!" data-beloworigin="true"
                   data-activates="content-dropdown2">Content<i class="material-icons right">arrow_drop_down</i></a>
            </li>
            <li><a class="dropdown-button" href="#!" data-beloworigin="true" data-activates="user-dropdown2">Users<i
                            class="material-icons right">arrow_drop_down</i></a></li>
            <li><a class="dropdown-button" href="#!" data-beloworigin="true" data-activates="order-dropdown2">Orders<i
                            class="material-icons right">arrow_drop_down</i></a></li>
            <li><a href="{{ route('admin:comment.index') }}" class='waves-effect'>Comments
                    @if (Cookie::get('comments') !== null)
                        @if (intval(Cookie::get('comments')) > 0)
                            <span id="newComments" class="new badge light-green">{{ Cookie::get('comments') }}</span>
                        @endif
                    @endif
                </a></li>
            @if(\Sentinel::check()->inRole('administrator'))
                <li>
                    <a class="dropdown-button" href="#!" data-beloworigin="true" data-activates="payment-dropdown2">
                        Payments<i class="material-icons right">arrow_drop_down</i>
                    </a>
                </li>
            @endif

            <li><a href='{{ route('admin:settings') }}' class='waves-effect'>Settings</a></li>
            <li><a href='{{ route('admin:logout') }}' class='waves-effect'>Log out</a></li>
        </ul>

        <ul id="nav-mobile" class="right side-nav" style="left: 0;">
            <li><a href='{{ route('admin:dashboard') }}' class='waves-effect'>Dashboard</a></li>
            <li><a class="dropdown-button" href="#!" data-beloworigin="true" data-activates="content-dropdown">Content<i
                            class="material-icons right">arrow_drop_down</i></a></li>
            <li><a class="dropdown-button" href="#!" data-beloworigin="true" data-activates="user-dropdown">Users<i
                            class="material-icons right">arrow_drop_down</i></a></li>
            <li><a class="dropdown-button" href="#!" data-beloworigin="true" data-activates="order-dropdown">Orders<i
                            class="material-icons right">arrow_drop_down</i></a></li>
            <li><a href="{{ route('admin:comment.index') }}" class='waves-effect'>Comments
                    @if (Cookie::get('comments') !== null)
                        @if (intval(Cookie::get('comments')) > 0)
                            <span class="new badge light-green">{{ Cookie::get('comments') }}</span>
                        @endif
                    @endif
                </a></li>
            @if(\Sentinel::check()->inRole('administrator'))
                <li><a class="dropdown-button" href="#!" data-beloworigin="true"
                       data-activates="payment-dropdown">Payments<i class="material-icons right">arrow_drop_down</i></a>
                </li>
            @endif
            <li><a href='{{ route('admin:settings') }}' class='waves-effect'>Settings</a></li>
            <li><a href='{{ route('admin:logout') }}' class='waves-effect'>Log out</a></li>
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

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="{{ asset('build/scripts/vendor.js') }}"></script>
{{-- Main script --}}
<script src="{{ elixir('admin/js/all.js') }}"></script>

{{-- Scripts --}}
@stack('scripts')
<script>
    $.fn.dataTable.ext.errMode = 'throw';
</script>
</body>
</html>
