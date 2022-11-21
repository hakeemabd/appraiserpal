@extends('user.entity')

@section('form-content')

    {!! Form::hidden('role', $role) !!}
    {!! Form::materialText('email', (isset($model)) ? $model->email : null) !!}
    {!! Form::materialPassword('password', null) !!}
    {!! Form::materialText('first_name', (isset($model)) ? $model->first_name : null) !!}
    {!! Form::materialText('last_name', (isset($model)) ? $model->last_name : null) !!}
    {!! Form::materialTel('mobile_phone', (isset($model)) ? $model->mobile_phone : null) !!}

@endsection