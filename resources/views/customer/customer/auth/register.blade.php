{!! Form::open([
'route' => 'customer:register',
'class' => 'registration-form',
'id' => 'registrationForm'
]) !!}

<div class="form-field">
    {!! Form::label('email', 'Email address (username)') !!}
    {!! Form::email('email', null, array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field">
    {!! Form::label('mobile_phone', 'Cell phone number') !!}
    {!! Form::tel('mobile_phone', null, array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field">
    {!! Form::label('password', 'Password') !!}
    {!! Form::password('password', array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field">
    {!! Form::label('password_confirmation', 'Confirm password') !!}
    {!! Form::password('password_confirmation', array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field">
    {!! Form::submit('Register', array('class' => 'btn btn-small form-placement-right')) !!}
</div>

{!! Form::close() !!}
