@extends('emails.layout', ['title' => 'Welcome', 'hasAction' => true])

@section('body')
You are now ready to receive orders! When you receive an order by email, just login to
<a href="{{ $host }}">appraiserssolutions.com</a> to accept the order and time given to complete the order.
<br>
<br>
Thank you, we appreciate you!
<br>
<br>
<br>
Appraisers Solutions
<br>
support@appraiserssolutions.com
@endsection
@section('action')
    <p>Please activate your account by visiting <a
                href="{{ route('worker:confirmSignup', ['email' => $user->email, 'code' => $activation->code]) }}"
                target="_blank">this
            link</a></p>
    <p>If you have troubles clicking the link, copy and paste this in your browser:<br/>
        {{ route('worker:confirmSignup', ['email' => $user->email, 'code' => $activation->code]) }}
    </p>
@endsection

