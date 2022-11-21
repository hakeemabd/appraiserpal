@extends('layouts.unauthorized')
@section('content')
    <h3 class="header center teal-text">Log in</h3>
    <div class="row">
        <div class="col s10 m10 l10 offset-l1">
            <div class="card-panel teal lighten-5">
                <div class="preloader-wrapper small">
                    <div class="spinner-layer spinner-green-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                {!! Form::open(['route' => 'admin:login', 'class' => 'auth-form']) !!}

                {!! Form::materialEmail('email', (isset($model)) ? $model->email : null) !!}
                {!! Form::materialPassword('password') !!}

                <div class="center-align">
                    {!! Form::submit('Log in', ['class' => 'btn waves-effect']); !!}
                    <a href="{{ route('admin:recoverPasswordForm') }}" class="waves-effect waves-teal btn-flat">Recover
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
        errorMessage: 'Login failed. Please check your credentials.'
    });
</script>
@endpush