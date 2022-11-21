<!--navigation-->

<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-navbar-collapse-1" aria-expanded="false"><span
                        class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="">
                <img class="logo-nav" alt="logo" src="/images/landing/appraiser_logo.png">
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
            <ul class="nav navbar-nav nav-left">
                <li><a href="#">About Us</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Pricing</a></li>
                <li><a href="#">FAQ</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if(Sentinel::guest())
                    <li>
                        <a href="javascript:void(0);"
                           class="popover-login">
                            <i class="fa fa-sign-in"></i>
                            Login
                        </a>
                    </li>
                    <li><a href="{{ route('customer:registration') }}" class="btn">JOIN US</a></li>
                @else
                    <li>
                        <div class="dropdown">
                            <button class="btn-skillet" type="button" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                {{ Sentinel::getUser()->fullName }}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                <li>
                                    <a href="{{ route('customer:profile') }}">My Profile</a>
                                </li>
                                <li>
                                    <a href="{{ route('customer:dashboard') }}">My Orders</a>
                                </li>
                                <li>
                                    <a href="{{ route('customer:logout') }}">Log out</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<!--navigation end-->