@extends('layouts.public')

@section('content')
    <h1>Error with your activation!</h1>
    <h3>{{ $error }}</h3>
    <a href="{{ route('worker:landing') }}">Home page</a>
@endsection