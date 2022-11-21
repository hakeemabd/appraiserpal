@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
This order was completed to the customers satisfaction. Check any feedback provided. Sign
into <a class="text-primary" href="{{ $host }}" target="_blank">admin site</a> to check feedback.
<br>
<br>
<br>
Appraisers Solutions
<br>
support@appraiserssolutions.com
@endsection

