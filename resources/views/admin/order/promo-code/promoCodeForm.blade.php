<?php
$isNew = isset($promoCode->id) ? false : true;
?>
@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:order.index') }}" class="breadcrumb">Orders</a>
    <a class="breadcrumb">{!! ($isNew) ? 'Create' : 'Update ' . $promoCode->id !!}</a>

@endsection

@section('content')

    {!! Form::open([
        'class' => 'form jvalidate-form',
    ]) !!}

    {!! Form::materialText('code', ((!$isNew) ? $promoCode->code : str_random(\App\Models\PromoCode::CODE_LENGTH)))  !!}
    {!! Form::materialNumber('percent', (!$isNew) ? $promoCode->percent : null) !!}
    {!! Form::materialNumber('count', (!$isNew) ? $promoCode->count : null) !!}

    {!! Form::submit(($isNew) ? 'Create' : 'Update', ['class' => 'btn btn-success']) !!}

    {!! Form::close() !!}
@endsection

@push('scripts')
<script>
    CJMA.DI.get('form').addForm({
        form: '.form',
        errorMessage: 'Something went wrong',
        successUrl: '{!! route('admin:promoCode.index') !!}',
        baseUrl: '{!! route('admin:promoCode.store', ['id' => ($isNew) ? false : $promoCode->id]) !!}'
    });
</script>
@endpush