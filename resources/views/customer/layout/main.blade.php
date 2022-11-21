<!DOCTYPE html>
<html>
<head>
    <title>{{ $pageTitle or "Appraiser Pal INC" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="/styles/customer.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    @stack('styles')
</head>
<body>
@yield('header')
@yield('content')
@stack('angular-scripts')
<script type="text/javascript">
    var __MSG = {};
    //###formatter:off
    @if (Session::has('__msg'))
        __MSG = {!! json_encode(Session::pull('__msg')) !!};
    @endif
    //###formatter:on
</script>
<script type="text/javascript" src="{{ elixir('customer/js/all.js') }}"></script>
@stack('scripts')
</body>
<!--Start of Zendesk Chat Script-->
<script type="text/javascript">
    $.fn.dataTable.ext.errMode = 'throw';
    window.$zopim || (function (d, s) {
        var z = $zopim = function (c) {
            z._.push(c)
        }, $ = z.s =
            d.createElement(s), e = d.getElementsByTagName(s)[0];
        z.set = function (o) {
            z.set._.push(o)
        };
        z._ = [];
        z.set._ = [];
        $.async = !0;
        $.setAttribute('charset', 'utf-8');
        $.src = 'https://v2.zopim.com/?4RBW4bgX3I2xLetyaI4xjohKKc7JZtNl';
        z.t = +new Date;
        $.type = 'text/javascript';
        e.parentNode.insertBefore($, e)
    })(document, 'script');
</script>
@if(isset($is_subsription))
    <script>

        $('.subscriptionChooseBtn').click(function () {
            var name = $(this).attr('data-name');
            var planId = $(this).attr('data-plan-id');
            $('input[name="plan_id"]').val(planId);
            var title = $('#' + name).find('.plan-title').text();
            $('input[name="plan_name"]').val(title);
            // var price = $('#' + name).find('.plan-price').text();
            var price = '$99';
            var description = $('#' + name).find('.price_text').text();
            $('.modal-plan-title').text(title);
            $('.modal-plan-price').text(price);
            $('.modal-plan-description').text(description);
        })
        if ($('select[name="use_existing_card"]').length) {
            toggleStripeCard($('select[name="use_existing_card"]').val())
        }
        $('select[name="use_existing_card"]').change(function () {
            toggleStripeCard($(this).val())
        })

        function toggleStripeCard(val) {
            if (val == '0') {
                $('#stripe_card').show();
            } else {
                $('#stripe_card').hide();
            }
        }

        var stripe = Stripe('{{env('STRIPE_API_KEY')}}');
        var elements = stripe.elements();
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var card = elements.create('card', {hidePostalCode: true, style: style});
        card.mount('#card-element');
        card.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if ($('select[name="use_existing_card"]').val() == '0' || $('select[name="use_existing_card"]').val() === undefined) {
                stripe.createToken(card).then(function (result) {
                    if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        card.addClass('StripeElement--invalid');
                    } else {
                        stripeTokenHandler(result.token);
                    }
                });
            } else {
                document.getElementById('payment-form').submit();
            }
        });
        // var cardNumber = elements.create('cardNumber');
        // cardNumber.mount('#card-number');
        // var cardExpiry = elements.create('cardExpiry');
        // cardExpiry.mount('#card-expiry');
        // var cardCvc = elements.create('cardCvc');
        // cardCvc.mount('#card-cvc');
        function stripeTokenHandler(token) {
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>
@endif
<!--End of Zendesk Chat Script-->
</html>
