{{--@todo upgrade view--}}
{!! Form::open([
    'route' => 'customer:updateUser',
    'class' => 'edit-profile-form',
    'novalidate' => 'novalidate'
]) !!}

<div class="form-field">
    {!! Form::label('first_name', 'First name') !!}
    {!! Form::text('first_name', $profile['first_name'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('last_name', 'Last name') !!}
    {!! Form::text('last_name', $profile['last_name'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('license_number', 'License Number') !!}
    {!! Form::number('license_number', $profile['license_number'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('mobile_phone', 'Cell Phone') !!}
    {!! Form::number('mobile_phone', $profile['mobile_phone'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('work_phone', 'Work Phone') !!}
    {!! Form::number('work_phone', $profile['work_phone'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('state', 'State') !!}
    @include('widgets.stateSelect', [
    'name' => 'state',
    'id' => 'state',
    'selected' => $profile['state']
    ])
</div>

<div class="form-field">
    {!! Form::label('city', 'City') !!}
    {!! Form::text('city', $profile['city'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('address_line_1', 'Address 1') !!}
    {!! Form::text('address_line_1', $profile['address_line_1'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('address_line_2', 'Address 2') !!}
    {!! Form::text('address_line_2', $profile['address_line_2'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('zip', 'Postal Code') !!}
    {!! Form::number('zip', $profile['zip'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::label('standard_instructions', 'Standard instructions') !!}
    {!! Form::textarea('standard_instructions', $profile['standard_instructions'], [
        'class' => 'form-control form-control-small form-control-grey'
    ]) !!}
</div>

<div class="form-field">
    {!! Form::submit('Save', array('class' => 'btn btn-small btn-primary form-placement-right')) !!}
</div>

{!! Form::close() !!}