<?php
$isNew = isset($reportType->id) ? false : true;
?>
@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:order.index') }}" class="breadcrumb">Orders</a>
    <a class="breadcrumb">{!! ($isNew) ? 'Create' : 'Update ' . $reportType->name !!}</a>

@endsection

@section('content')

    {!! Form::open([
        'class' => 'form jvalidate-form',
    ]) !!}

    {!! Form::materialText('name', ((!$isNew) ? $reportType->name : null))  !!}
    {!! Form::materialNumber('current_price', (!$isNew) ? $reportType->current_price : null) !!}
    {!! Form::materialNumber('old_price', (!$isNew) ? $reportType->old_price : null) !!}

    {!! Form::submit(($isNew) ? 'Create' : 'Update', ['class' => 'btn btn-success']) !!}

    {!! Form::close() !!}
@endsection

@push('scripts')
<script>
    CJMA.DI.get('form').addForm({
        form: '.form',
        errorMessage: 'Something went wrong',
        successUrl: '{!! route('admin:reportType.index') !!}',
        baseUrl: '{!! route('admin:reportType.store', ['id' => ($isNew) ? false : $reportType->id]) !!}'
    });
</script>
@endpush