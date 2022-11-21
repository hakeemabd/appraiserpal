@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
The invitation sent to {{$worker}} has reached its idle time and was not accepted. The order
needs to be reassigned immediately. Sign into <a class="text-primary" href="{{ $host }}" target="_blank">admin site</a> to reassign.
<br>
<br>
Appraisers Solutions
<br>
support@appraiserssolutions.com
@endsection

