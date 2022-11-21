@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('worker:order.index') }}" class="breadcrumb">Orders</a>
    <form class="right">
        <div class="input-field">
            <input id="search" type="search" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>

@endsection

@section('content')

    {{-- Datatable --}}
    <table class="centered striped datagrid" data-source="{{ route('worker:order.data') }}">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th data-index="time_left">Time left</th>
            <th data-render="orderStatus">Status</th>
        </tr>
        </thead>
    </table>

@endsection

@push('scripts')

<script type="text/javascript">
    CJMA.DI.get('datagrid').addGrid({
        orderStatusRenderer: function (data, type, row) {
            if (type !== 'display') {
                return data;
            }
            return row.rendered_status;
        },
        search: '#search',
        responsive: true
    });
</script>

@endpush