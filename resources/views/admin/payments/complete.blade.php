@extends('layouts.page')

@section('breadcrumb')

    <a class="breadcrumb">Completed Payments</a>
    <form class="right">
        <div class="input-field">
            <input id="search" type="search" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>

@endsection

@section('content')
    <table class="centered striped datagrid" data-source="{{ route('admin:payments', ['status' => 'complete']) }}" data-action="0">
        <thead>
        <tr>
            <th data-index="col_title">Title</th>
            <th data-index="col_worker">Worker</th>
            <th data-index="col_date">Date</th>
            <th data-index="col_cost">Cost($)</th>
        </tr>
        </thead>
    </table>

@endsection

@push('scripts')
<script type="text/javascript">
    var datatables = null;

    datatables = CJMA.DI.get('datagrid').addGrid({responsive: true, actions: true})
</script>
@endpush