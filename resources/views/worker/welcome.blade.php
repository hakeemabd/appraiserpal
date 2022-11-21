@extends('layouts.public')

@section('content')
    <div id="index-banner" class="parallax-container">
        <div class="section no-pad-bot">
            <div class="container">
                <br><br>

                <h1 class="header center teal-text text-lighten-2">Appraisers Solutions</h1>

                <div class="row center">
                    <h5 class="header col s12 light">Join us today!</h5>
                </div>
                <div class="row center">
                    <a href="{{ route('worker:registration') }}"
                       class="btn-large waves-effect waves-light teal lighten-1">Get Started</a>
                </div>
                <br><br>

            </div>
        </div>
        <div class="parallax"><img src="/images/background1.jpg" alt="Unsplashed background img 1"></div>
    </div>
@endsection
