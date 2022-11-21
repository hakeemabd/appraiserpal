{!! Form::open([
    'route' => 'customer:login',
    'class' => 'auth-form',
    'novalidate' => 'novalidate'
]) !!}

<div class="form-field">
    {!! Form::label('email', 'Email address') !!}
    {!! Form::email('email', null, array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field without-padding">
    {!! Form::label('password', 'Password') !!}
    {!! Form::password('password', array ('class' => 'form-control form-control-small form-control-grey')) !!}
</div>

<div class="form-field">
    <a href="javascript:void(0)" class="recover-password">
        I forgot my password
    </a>
</div>

<div class="form-field">
    {!! Form::submit('Log in', array('class' => 'btn btn-small form-placement-right')) !!}
</div>

{!! Form::close() !!}
