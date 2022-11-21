@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
This order is complete and needs to be sent to customer. Login to <a class="text-primary" href="{{ $host }}" target="_blank">admin site</a>.
<br>
<br>
<br>
Appraisers Solutions
<br>
support@appraiserssolutions.com
@endsection