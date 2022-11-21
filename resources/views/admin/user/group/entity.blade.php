@extends('layouts.entity')

@section('breadcrumb')

    <a href="{{ route('admin:workerGroup.index') }}" class="breadcrumb">Worker Groups</a>
    <a class="breadcrumb">@if(isset($model)) Edit {{ $model->name }} @else Create @endif</a>

@endsection

@section('form-content')
    {!! Form::materialText('name', (isset($model)) ? $model->name : null, ['label' => 'Title']) !!}
    {!! Form::materialNumber('sort', (isset($model)) ? $model->sort : null, ['label' => 'Sequence']) !!}
@endsection

@push('scripts')
<script>
    CJMA.DI.get('form').addForm({
        baseUrl: '/workerGroup',
        modelId: {{ isset($model) ? $model->id : 'null' }},
        successUrl: '{{ route('admin:workerGroup.index')  }}',
        errorMessage: 'Saving group failed. Please correct validation errors.'
    });
</script>

@endpush