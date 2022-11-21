@extends('layouts.public')
@section('content')
    <h3 class="header center teal-text">Log in</h3>
    <div class="row">
        <div class="col s10 m10 l10 offset-l1">
            <div class="card-panel teal lighten-5">
                {!! Form::open(['route' => 'worker:login', 'class' => 'auth-form']) !!}

                {!! Form::materialEmail('email', (isset($model)) ? $model->email : null) !!}
                {!! Form::materialPassword('password') !!}

                <div class="center-align" style="clear:both">
                    {!! Form::submit('Log in', ['class' => 'btn waves-effect']) !!}
                    <a href="{{ route('worker:recoverPasswordForm') }}" class="waves-effect waves-teal btn-flat">Recover
                        password</a>
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
        successUrl: '/',
        errorMessage: 'Login failed. Please check your credentials.'
    });
</script>
@endpush