{!! Form::open([
'route' => 'customer:recoverPassword',
'class' => 'recoverForm'
]) !!}

<div class="form-field without-padding">
    {!! Form::label('email', 'Enter Email for password recovery') !!}
    {!! Form::email('email', null, array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field">
    <a href="javascript:void(0)" class="back-to-login">
        Back to login
    </a>
</div>

<div class="form-field">
    {!! Form::submit('Send', array('class' => 'btn btn-small form-placement-right')) !!}
</div>

{!! Form::close() !!}
