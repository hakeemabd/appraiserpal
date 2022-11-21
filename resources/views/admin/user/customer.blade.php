@extends('user.entity')

@section('form-content')

    {!! Form::hidden('role', $role) !!}
    {!! Form::materialText('email', (isset($model)) ? $model->email : null) !!}
    {!! Form::materialPassword('password', null) !!}
    {!! Form::materialText('first_name', (isset($model)) ? $model->first_name : null) !!}
    {!! Form::materialText('last_name', (isset($model)) ? $model->last_name : null) !!}
    {!! Form::materialTel('mobile_phone', (isset($model)) ? $model->mobile_phone : null) !!}
    {!! Form::materialTel('work_phone', (isset($model)) ? $model->work_phone : null) !!}
    {!! Form::materialNumber('license_number', (isset($model)) ? $model->license_number : null) !!}
    <div class="input-field col s8 m9 offset-s2 offset-m1 offset-l2 l8"><div class="divider"></div></div>
    {!! Form::materialText('address_line_1', (isset($model)) ? $model->address_line_1 : null) !!}
    {!! Form::materialText('address_line_2', (isset($model)) ? $model->address_line_2 : null) !!}
    {!! Form::materialText('city', (isset($model)) ? $model->city : null) !!}
    {!! Form::materialUsState('state', (isset($model)) ? $model->state : null) !!}
    {!! Form::materialText('zip', (isset($model)) ? $model->zip : null) !!}
    <div class="input-field col s8 m9 offset-s2 offset-m1 offset-l2 l8"><div class="divider"></div></div>
    {!! Form::materialCheckbox('auto_comments', 1, (isset($model)) ? $model->auto_comments : null) !!}
    {!! Form::materialCheckbox('auto_invite', 1, (isset($model)) ? $model->auto_invite : null) !!}
    {!! Form::materialCheckbox('auto_delivery', 1, (isset($model)) ? $model->auto_delivery : null) !!}
    {!! Form::materialCheckbox('email_notification', 1, (isset($model)) ? $model->email_notification : null) !!}
    {!! Form::materialCheckbox('sms_notification', 1, (isset($model)) ? $model->sms_notification : null) !!}
    {!! Form::materialCheckbox('confirmed', 1, (isset($model)) ? $model->confirmed : null, ['label' => 'Published']) !!}
    {!! Form::materialNumber('delayed_payment', (isset($model)) ? $model->delayed_payment : null, empty($model->stripe_id) ? ['disabled' => 'disabled'] : []) !!}
    {!! Form::materialTextarea('notes', (isset($model)) ? $model->notes : null) !!}

@endsection