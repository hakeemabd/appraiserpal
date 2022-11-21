<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="http://ap-la.local/scripts/jquery.payment.js"></script>

    {{--<script>--}}
    {{--jQuery(function($) {--}}
    {{--$('[data-numeric]').payment('restrictNumeric');--}}
    {{--$('.cc-number').payment('formatCardNumber');--}}
    {{--$('.cc-exp').payment('formatCardExpiry');--}}
    {{--$('.cc-cvc').payment('formatCardCVC');--}}
    {{--$.fn.toggleInputError = function(erred) {--}}
    {{--this.parent('.form-group').toggleClass('has-error', erred);--}}
    {{--return this;--}}
    {{--};--}}
    {{--$('form').submit(function(e) {--}}
    {{--e.preventDefault();--}}
    {{--var cardType = $.payment.cardType($('.cc-number').val());--}}
    {{--$('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));--}}
    {{--$('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));--}}
    {{--$('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));--}}
    {{--$('.cc-brand').text(cardType);--}}
    {{--$('.validation').removeClass('text-danger text-success');--}}
    {{--$('.validation').addClass($('.has-error').length ? 'text-danger' : 'text-success');--}}
    {{--});--}}
    {{--});--}}
    {{--</script>--}}
</head>
<body>
<form action="/charge" method="POST">
    <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="pk_test_Myuv1KPev5POBDyBPTj78mWX"
            data-image="/img/documentation/checkout/marketplace.png"
            data-name="Demo Site"
            data-description="2 widgets"
            data-amount="2000"
            data-locale="auto">
    </script>
</form>
</body>
</html>