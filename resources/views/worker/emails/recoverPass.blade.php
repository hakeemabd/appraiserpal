@extends('emails.layout', ['title' => 'Recover password', 'hasAction' => true])

@section('body')

    <h1>Hello!</h1>

    <p class="lead">You have requested a password reset</p>

@endsection

@section('action')
    <p>Please follow <a
                href="{{ route('worker:changePasswordForm', ['email' => $user->email, 'code' => $reminder->code]) }}"
                target="_blank">this
            link</a> to reset your password</p>
@endsection

