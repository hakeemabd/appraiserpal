@extends('layout.main')

@push('styles')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="{{ asset('build/styles/vendor.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('build/styles/app.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('styles/customer.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
<script type="text/javascript">
    //###formatter:off **it is here for reason, phpstorm cannot autoformat this correctly**
    var AuthorizedUser = {!! json_encode(Sentinel::check()->getProfile()) !!};

    var event = new CustomEvent("userReady", {detail:AuthorizedUser});

    document.dispatchEvent(event);
    //###formatter:on
</script>
@endpush

@section('header')
    <header class="un-conflict-wrapper">
        <div class="container">
            @include('widgets.authHeader')
        </div>
    </header>
@endsection
