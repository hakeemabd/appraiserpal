<div class="wrapper-lg bg-white-opacity">
    <div class="row m-t">
        <div class="col-sm-7">
            <a href="javascript:void(0);" class="thumb-lg pull-left m-r">
                <img src="{{ isset($profile['avatar']) ? $profile['avatar'] : '/images/profile.png' }}"
                     class="img-circle">
            </a>

            <div class="clear m-b">
                <div class="m-b m-t-sm">
                    <span class="h3 text-black">{{$profile['first_name']}}&nbsp;{{$profile['last_name']}}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper bg-white b-b">
    <ul class="nav nav-pills nav-sm">
        <li class="{{ set_active(route('customer:profile', [], false)) }}"><a href="{{ route('customer:profile') }}">Personal Info</a></li>
        <li><a href="#">Billing info</a></li>
        <li class="{{ set_active(route('customer:profileEdit', [], false)) }}"><a href="{{ route('customer:profileEdit', [], false) }}#fileManager/clone">Files management</a></li>
    </ul>
</div>