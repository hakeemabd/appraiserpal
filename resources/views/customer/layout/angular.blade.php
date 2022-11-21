@extends('layout.authorized')

@push('styles')


@endpush

@push('scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="{{ asset('build/scripts/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('build/scripts/app.js') }}"></script>
@endpush

@section('content')
    <div ui-view=""></div>
@endsection