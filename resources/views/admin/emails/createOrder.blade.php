@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
<img src="<?php echo $message->embed(public_path().'/images/Appraiser.png'); ?>" style="display: block; margin-right: auto;" width="100">
<hr/>
Thank you {{ $customer }}!
<br>
<br>
Your order has been created! Please login to your dashboard at <a class="text-primary" href="{{ $host }}" target="_blank">www.appraiserpal.com</a>
to check the status of your order, under "my orders".
<br>
<br>
Thank you, we appreciate your business!
<br>
<br>
<br>
Appraiser Pal Support
<br>
info@appraiserpal.com
@endsection

