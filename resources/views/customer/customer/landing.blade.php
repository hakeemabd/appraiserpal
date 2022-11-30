@extends('layout/main')

@push('styles')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="/styles/landing.css" rel="stylesheet">
@endpush
@section('content')
@include('customer.landing.heroHeader')
@include('customer.landing.featuredOn')
@include('customer.landing.benefits')
@include('customer.landing.features')
@include('customer.landing.tour')
@include('customer.landing.reviews')
@include('customer.landing.pricing')
@include('customer.landing.footer')
@include('customer.landing.tpl')
@endsection