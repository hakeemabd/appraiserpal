<!--Pricing-->
<div class="full-container" id="pricing">
    <div class="container section_prices">
        <h1>Check Out Our Payment Plans</h1>
        @if(!isset($is_subsription))
            <h4>Get 1 order free during 7 day trial period</h4>
        @endif
    </div>
    <div class="container section_prices">
        <div class="col-sm-12 col-md-12 col-lg-2"></div>
        <div class="col-sm-6 col-md-6 col-lg-4 col-sm-offset-3 col-md-offset-4 col-lg-offset-2" id="single">
            <div class="prices">
                <h3 class="plan-title">appraisal reports</h3>
                <span class="price">
<label class="plan-price">$79*</label>
                    {{--<span>$79</span>--}}
</span>
                <div class="price_text">
                    Per Appraisal Report
                    (Fannie Mae Forms 1004, 1073, 2055, 1075)
                </div>
                <ul>
                    <li>Trained and Experienced Expert</li>
                    <li>24 hour turnaround*</li>
                    <li>Adjustments</li>
                    <li>Guaranteed Satisfaction!</li>
                </ul>
                <div class="price_btn">
                    @if(isset($is_subsription))
                        <button @if(!$user->subscribed() || (empty($user->stripe_subscription) && empty($user->stripe_plan))) data-toggle="modal"
                                data-plan-id="{{\App\User::SUBSCRIPTION_PLAN_ID}}"
                                data-name="single"
                                data-target="#paymentModal"
                                @endif
                                class="btn btn-default subscriptionChooseBtn btn-submit">Choose
                        </button>
                    @else
                        <a href="#" class="btn btn-default btn-submit">SIGN UP</a>
                    @endif
                </div>
            </div>
            <div class="subject_change">*subject to change</div><br/>
            <div class="subject_change" style="float: initial;">*based on members $99 member fee</div>
            {{--<div class="subject_change" style="float: initial;">**price is based on 20 reports per month volume</div>--}}
        </div>
        {{--<div class="col-sm-6 col-md-6 col-lg-4" id="multi">--}}
        {{--<div class="prices">--}}
        {{--<h3 class="plan-title">2-4 units report</h3>--}}
        {{--<span class="price">--}}
        {{--<label class="plan-price">$99</label> <span>$109</span>--}}
        {{--</span>--}}
        {{--<div class="price_text">--}}
        {{--2-4 units appraisal report--}}
        {{--(small residential income report.--}}
        {{--Fannie Mae Form 1025))--}}
        {{--</div>--}}
        {{--<ul>--}}
        {{--<li>Licensed Appraiser Expert</li>--}}
        {{--<li>24 hour turnaround*</li>--}}
        {{--<li>Adjustments</li>--}}
        {{--<li>Guaranteed Satisfaction!</li>--}}
        {{--</ul>--}}
        {{--<div class="price_btn">--}}
        {{--@if(isset($is_subsription))--}}
        {{--<button data-toggle="modal" data-plan-id="plan_FaeMypFgphUyI6" data-name="multi"--}}
        {{--data-target="#paymentModal"--}}
        {{--class="btn btn-default subscriptionChooseBtn btn-submit">Choose--}}
        {{--</button>--}}
        {{--@else--}}
        {{--<a href="#" class="btn btn-default btn-submit">SIGN UP</a>--}}
        {{--@endif--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--<div class="subject_change visible-xs">*subject to change</div>--}}

        {{--</div>--}}
        <div class="col-sm-12 col-md-12 col-lg-2"></div>

    </div>
</div>
@if(isset($is_subsription))
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" style="text-align: left" id="myModalLabel">Payment</h4>
                </div>
                <form action="{{url('/subscribe')}}" method="post" id="payment-form">
                    {{csrf_field()}}
                    <input type="hidden" name="plan_id" value="" required/>
                    <input type="hidden" name="plan_name" value="" required/>
                    <div class="modal-body">
                        @if($user->subscribed())
                            <div class="form-row">
                                <div class="form-group">
                                    <select name="use_existing_card" class="form-control">
                                        <option value="1">Use Existing Card *** {{$user->last_four}}</option>
                                        <option value="0">New Card</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="form-row" id="stripe_card" @if($user->subscribed())style="display: none"@endif>
                            <label for="card-element">
                                Credit or debit card
                            </label>
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>

                            <!-- Used to display Element errors. -->
                            <div id="card-errors" role="alert"></div>
                        </div>
                        <div class="form-row" style="margin-top: 35px;">
                            <h4 style="text-align: left">Plan Information</h4>
                            <div class="row" style="margin-top: 20px;margin-bottom: 5px;">
                                <div class="col-md-3">
                                    <b>Plan Name :</b>
                                </div>
                                <div class="col-md-9">
                                    <span class="modal-plan-title"></span>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3">
                                    <b>Price :</b>
                                </div>
                                <div class="col-md-9">
                                    <span class="modal-plan-price"></span>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3">
                                    <b>Description :</b>
                                </div>
                                <div class="col-md-9">
                                    <span class="modal-plan-description"></span>
                                </div>
                            </div>
                            {{--<div class="row" style="margin-bottom: 5px;">--}}
                            {{--<div class="col-md-3">--}}
                            {{--<b>Trial Period :</b>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-9">--}}
                            {{--<span>7 Days</span>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
<!--Pricing end-->