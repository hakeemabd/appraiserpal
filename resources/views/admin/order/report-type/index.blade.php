@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:order.index') }}" class="breadcrumb">Orders</a>
    <a class="breadcrumb">Report Types</a>

@endsection

@section('content')
    <div class="col s12">
        <table class="centered striped datagrid"
            data-source="{{ route('admin:reportType.view') }}">
            <thead>
            <tr>
                <th data-index="id">Id</th>
                <th>Name</th>
                <th data-index="current_price">Current Price</th>
                <th data-index="old_price">Old Price</th>
            </tr>
            </thead>
        </table>
    </div>

    <div class="fixed-action-btn">
        <a href="{{ route('admin:reportTypeCreate') }}" class="btn-floating btn-large pink waves-effect">
            <i class="large material-icons">add</i>
        </a>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('datagrid').addGrid({
        success: {
            'delete': 'Report Type successfully deleted'
        },
        error: {
            'delete': 'Report Type is not deleted'
        }
    });
</script>
@endpush