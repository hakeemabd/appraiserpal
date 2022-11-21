@extends('layouts.entity')

@section('breadcrumb')

    <a href="{{ route('admin:user.index') }}" class="breadcrumb">Users</a>
    <span class="breadcrumb breadcrumb-etc">...</span>
    <a href="{{ route('admin:usersList', ['role' => $role]) }}" class="breadcrumb"> {{ ucfirst($role).'s' }}</a>
    <a class="breadcrumb">@if(isset($model)) Edit @else Create @endif</a>

@endsection

@push('scripts')


<script>
    CJMA.DI.get('form').addForm({
        baseUrl: '/user',
        modelId: {{ isset($model) ? $model->id : 'null' }},
        successUrl: '{{ route('admin:usersList', ['role' => $role])  }}',
        errorMessage: 'Saving user failed. Please correct validation errors.'
    });
</script>

@endpush