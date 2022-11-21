@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
Hello,
<br>
<br>
There is a new message regarding {{$data['orderTitle']}}
<br>
<br>
Please see the new message below:
<br>
<br>
{{ $data['content'] }}
<br>
<br>
To respond to this message, go to this order in your dashboard by logging in here 
<a class="text-primary" href="{{ $data['custmerHost'] }}" target="_blank">appraiserpal</a>
<br>
<br>
Thank you, we appreciate you!
<br>
<br>
<br>
Appraiser Pal Support
<br>
info@appraiserpal.com
@endsection