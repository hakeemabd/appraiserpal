@if(Sentinel::guest())
    <li><a href="{{ route('worker:login') }}">Log in</a></li>
@else
    <li><a href="{{ route('worker:dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('worker:logout') }}">Log out</a></li>
@endif