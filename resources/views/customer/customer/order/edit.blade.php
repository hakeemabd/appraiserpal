@extends('layout.angular')
@push('scripts')
<script type="text/javascript">
    //###formatter:off
    var FILE_UPLOAD_CONFIG = {!! json_encode($uploadConfig, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
    var STRIPE_KEY = {!! json_encode(App\Helpers\StripeHelper::getStripeKey()) !!};

    //###formatter:on
</script>
@endpush

@section('content')
    <div ui-view=""></div>
@endsection