@extends('layouts.page')

@section('breadcrumb')

    <a class="breadcrumb">Customer Payments</a>
    <form class="right">
        <div class="input-field">
            <input id="search" type="search" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>

@endsection

@section('content')
    <div class="row">
        <div class="col s12">
          <ul class="tabs">
            <li class="tab col s3"><a id="tab-public" class="active" href="#dueTable">Due</a></li>
            <li class="tab col s3"><a id="tab-private" href="#completeTable">Complete</a></li>
          </ul>
        </div>
        <div id="dueTable" class="col s12">
        <table class="centered striped datagrid due" data-source="{{ route('admin:transactions', ['status' => 'due']) }}" data-action="0">
            <thead>
            <tr>
                <th data-index="col_title">Title</th>
                <th data-index="col_customer">Customer</th>
                <th data-index="col_date">Date</th>
                <th data-index="col_cost">Cost($)</th>
            </tr>
            </thead>
        </table>
        </div>
        <div id="completeTable" class="col s12">
            <table class="centered striped datagrid complete" data-source="{{ route('admin:transactions', ['status' => 'complete']) }}" data-action="0">
                <thead>
                <tr>
                    <th data-index="col_title">Title</th>
                    <th data-index="col_customer">Customer</th>
                    <th data-index="col_date">Date</th>
                    <th data-index="col_cost">Cost($)</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script type="text/javascript">
    var datatables = null;

    CJMA.DI.get('datagrid').addGrid({responsive: true, actions: true})
</script>
@endpush