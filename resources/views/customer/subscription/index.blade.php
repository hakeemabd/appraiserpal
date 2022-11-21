@extends('layout.authorized')
@section('content')
    <style>
        .btn-submit {
            background: #fe9800;
            font-size: 18px;
            color: #fff !important;
            width: 80%;
            border-color: #fe9800;
            font-family: Montserrat;
        }

        .StripeElement {
            box-sizing: border-box;

            height: 40px;

            padding: 10px 12px;

            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;

            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

        #card-errors {
            color: red;
            margin-top: 10px
        }
    </style>
    <div class="container">
        <div class="row wrapper">
            {{--@if($user->subscribed())--}}
            <div class="btn-group pull-right" role="group">
                <a href="{{ url('/dashboard') }}" class="btn btn-warning">Back</a>
            </div>
            {{--@endif--}}
            @include('landing.pricing')
        </div>
    </div>
@endsection