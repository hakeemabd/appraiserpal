@extends('layout/main')

@push('styles')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="/styles/landing.css" rel="stylesheet">
@endpush
@section('content')
    @include('landing.heroHeader')
    @include('landing.featuredOn')
    @include('landing.benefits')
    @include('landing.features')
    @include('landing.tour')
    @include('landing.reviews')
    @include('landing.pricing')
    @include('landing.footer')
    @include('landing.tpl')
@endsection