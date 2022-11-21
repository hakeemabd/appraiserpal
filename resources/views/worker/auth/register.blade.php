@extends('layouts.public')
@section('content')
    <h3 class="header center teal-text">Register</h3>
    <div class="row">
        <div class="col s10 m10 l10 offset-l1">
            <div class="card-panel teal lighten-5">
                {!! Form::open(['route' => 'worker:registration', 'class' => 'auth-form']) !!}
                {!! Form::materialEmail('email', (isset($model)) ? $model->email : null) !!}

                {!! Form::materialTel('mobile_phone', (isset($model)) ? $model->mobile_phone : null) !!}

                {!! Form::materialPassword('password') !!}

                {!! Form::materialPassword('password_confirmation') !!}

                <div class="center-align" style="clear:both">
                    {!! Form::submit('Register', ['class' => 'btn waves-effect']) !!}
                    <a href="{{ route('worker:login') }}" class="waves-effect waves-teal btn-flat">Back to login</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('form').addForm({
        form: '.auth-form',
        errorMessage: 'Registration failed. Please correct errors.'
    });
</script>
@endpush
