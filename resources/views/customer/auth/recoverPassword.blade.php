@extends('customer.layout.main')

@push('styles')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="{{ asset('build/styles/vendor.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('build/styles/app.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
	<div class="container card" style="max-width: 80%; padding: 20px; margin-top: 50px;">
		{!! Form::open([
		'route' => 'customer:recoverPassword',
		'class' => 'recoverForm'
		]) !!}

		<div class="form-field without-padding">
		    <h3>{!! Form::label('email', 'Enter Email for password recovery') !!}</h3>
		    {!! Form::email('email', null, array ('class' => 'form-control form-control-small form-control-grey')) !!}
		</div>

		<div class="form-field">
		    <a href="/" class="back-to-login">
		        Back to login
		    </a>
		</div>

		<div class="form-field">
		    {!! Form::submit('Send', array('class' => 'btn btn-small form-placement-right')) !!}
		</div>

		{!! Form::close() !!}
	</div>
@endsection

@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('form').addForm({
        form: '.recoverForm',
        successUrl: '/',
        errorMessage: 'Can\'t recover your password. Please check your email.'
    });
</script>
@endpush
