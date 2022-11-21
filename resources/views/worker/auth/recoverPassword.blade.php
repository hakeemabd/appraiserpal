@extends('layouts.public')
@section('content')
    <h3 class="header center teal-text">Recover password</h3>
    <div class="row">
        <div class="col s10 m10 l10 offset-l1">
            <div class="card-panel teal lighten-5">
                {!! Form::open(['route' => 'worker:recoverPassword', 'class' => 'auth-form']) !!}

                {!! Form::materialEmail('email', (isset($model)) ? $model->email : null) !!}

                <div style="clear:both"></div>

                <div class="center-align">
                    {!! Form::submit('Send', ['class' => 'btn waves-effect']); !!}
                    <a href="{{ route('worker:login') }}" class="waves-effect waves-teal btn-flat">Back</a>
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
        errorMessage: 'Can\'t recover your password. Please check your email.'
    });
</script>
@endpush