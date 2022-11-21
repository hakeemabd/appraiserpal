@extends('admin.emails.layout', ['hasAction' => false])

@section('body')
    <img src="<?php echo $message->embed(public_path() . '/images/Appraiser.png'); ?>"
         style="display: block; margin-right: auto;" width="100">
    <hr/>
    {!! $task !!}
    <br>
    <br>
    <br>
    Appraiser Pal Support
    <br>
    info@appraiserpal.com
@endsection

