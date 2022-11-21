@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
The customer has requested revisions. Sign into 
You can see the details of order in the following link: <a class="text-primary" href="{{ $host }}" target="_blank">admin site</a> to assign a worker.
<br>
<br>
<br>
Appraisers Solutions
<br>
support@appraiserssolutions.com
@endsection

