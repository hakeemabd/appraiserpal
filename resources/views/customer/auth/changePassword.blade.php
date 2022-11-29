@extends('customer.layout.main')

@push('styles')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="{{ asset('build/styles/vendor.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('build/styles/app.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
    <div class="container card" style="max-width: 80%; padding: 20px; margin-top: 50px;">
        <h2>Recover password</h2>
        {!! Form::open(['route' => 'customer:changePassword', 'class' => 'auth-form']) !!}

        {!! Form::hidden('email', $email) !!}
        {!! Form::hidden('code', $code) !!}


        <div class="form-field">
            {!! Form::label('password', 'New password') !!}
            {!! Form::password('password',
            ['class' => 'form-control form-control-small form-control-base form-control-grey']) !!}
        </div>

        <div class="form-field">
            {!! Form::label('password_confirmation', 'Confirm it') !!}
            {!! Form::password('password_confirmation',
            ['class' => 'form-control form-control-small form-control-base form-control-grey']) !!}
        </div>

        <div class="form-field">
            {!! Form::submit('Change password', array('class' => 'btn btn-small btn-primary')) !!}
        </div>

        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('form').addForm({
        form: '.auth-form',
        successUrl: '/',
        errorMessage: 'Can\'t change password.'
    });
</script>
@endpush
