@extends('layouts.unauthorized')
@section('content')
    <h3 class="header center teal-text">Set new password</h3>
    <div class="row">
        <div class="col s10 m10 l10 offset-l1">
            <div class="card-panel teal lighten-5">
                {!! Form::open(['route' => 'admin:changePassword', 'class' => 'auth-form']) !!}
                {!! Form::hidden('email', $email) !!}
                {!! Form::hidden('code', $code) !!}

                {!! Form::materialPassword('password', null) !!}
                {!! Form::materialPassword('password_confirmation', null, ['label' => 'Confirmation']) !!}

                <div class="center-align">
                    {!! Form::submit('Change', ['class' => 'btn waves-effect']); !!}
                    <a href="{{ route('admin:login') }}" class="waves-effect waves-teal btn-flat">Cancel</a>
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
        errorMessage: 'Can\'t change password.'
    });
</script>
@endpush