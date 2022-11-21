@extends('layout.authorized')

@push('styles')


@endpush

@push('angular-scripts')
<script type="text/javascript">
    var FILE_UPLOAD_CONFIG = {!! json_encode($uploadConfig, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
    var STRIPE_KEY = {!! json_encode(App\Helpers\StripeHelper::getStripeKey()) !!};
</script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="{{ asset('build/scripts/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('build/scripts/app.js') }}"></script>
@endpush

@section('content')
    <div class="container">
        <!-- uiView:  -->
        <div class="app-content-body fade-in-up" style="padding-bottom: 0;"><!-- uiView:  -->
            <div class="fade-in-down ">
                <div class="hbox hbox-auto-xs hbox-auto-sm ">
                    <div class="col">
                        @include('widgets.profileHeader', ['profile' => $user])
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden" id="editProfileTpl">
        @include('profile.editProfile', ['profile' => $user])
    </div>
    <div ui-view=""></div>
@endsection
