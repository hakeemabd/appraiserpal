@extends('layout/main')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    @endpush

    @include('customer.landing.heroHeader')

    <div class="container">
        <div style="margin-top: 190px;margin-bottom: 90px;">
            {!! $page !!}
        </div>
    </div>

    @include('customer.landing.footer')
@endsection