@extends('layouts.public')

@section('content')
    <h1>Thank you for activating!</h1>
    <a href="{{ route('worker:dashboard') }}">Continue to your dashboard</a>
@endsection