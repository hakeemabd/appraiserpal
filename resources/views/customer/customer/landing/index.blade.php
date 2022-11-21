@extends('layout/main')

@section('content')
    @push('styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    @endpush

    @include('landing.heroHeader')
    @include('landing.featuredOn')
    @include('landing.benefits')
    @include('landing.features')
    @include('landing.reviews')
    @include('landing.pricing')
    @include('landing.footer')

    <!--------------------->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="padding-top:8%;">
            <div class="modal-content">
                <div class="modal-header" style="border:none;">
                    <button type="button" style="color:#000;" class="close" data-dismiss="modal" aria-hidden="true">
                        ×</button>
                </div>
                <div class="modal-body" style="padding-bottom:50px;">
                    <div class="row">
                        <div class="col-sm-12" >
                            <form role="form" class="form-horizontal auth-form" style="max-width:70%; margin:0 auto;" action="{{ route('customer:login') }}" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <h2 style="color:#000; text-align:left; margin-bottom:25px;">LOGIN</h2>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <input type="email" class="form-control" id="email1" placeholder="Email" name="email"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                       
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary btn-sm btn-submit" style="width:150px;">
                                                Submit</button>
                                         
                                        </div>
                                    </div>
                                    </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---------------------->
@endsection