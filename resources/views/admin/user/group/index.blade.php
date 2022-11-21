@extends('layouts.page')

@section('breadcrumb')

    <a class="breadcrumb">Worker groups</a>

@endsection

@section('content')

    {{-- Datatable --}}
    <table class="centered striped datagrid" data-source="{{ route('admin:workerGroup.data') }}">
        <thead>
        <tr>
            <th data-index="sort">Sequence</th>
            <th data-index="name">Title</th>
        </tr>
        </thead>
    </table>

    {{-- FAB --}}
    <div class="fixed-action-btn">
        <a href="{{ route('admin:workerGroup.create') }}" class="btn-floating btn-large pink waves-effect">
            <i class="large material-icons">add</i>
        </a>
    </div>

@endsection

@push('scripts')

<script type="text/javascript">
    CJMA.DI.get('datagrid').addGrid({
        success: {
            'delete': 'Group was successfully removed.'
        },
        error: {
            'delete': 'Cannot remove group. Do you have someone working in it at the moment?'
        }
    });

    </script>
@endpush