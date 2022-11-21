@extends('worker.emails.layout', ['hasAction' => true])

@section('body')
    You have been invited to work an appraisal report. Please sign in to your dashboard at <a class="text-primary" href="{{ $host }}" target="_blank">appraiserssolutions.com</a> to accept this order.
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

