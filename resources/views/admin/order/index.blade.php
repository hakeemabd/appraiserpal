@extends('layouts.page')

@section('breadcrumb')
    <a href="{{ route('admin:order.index') }}" class="breadcrumb">Orders</a>
    <form class="right">
        <div class="input-field">
            <input id="search" type="search" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>

@endsection

@section('content')
    <table class="centered datagrid orders" data-source="{{ route('admin:order.data') }}">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th data-index="created_at" data-render="usDateTime">Ordered On</th>
            <th data-index="orderTimeLeft">Time left</th>
            <th data-index="transaction_status">Payment</th>
            <th data-index="user.fullName">Customer</th>
            <th data-index="rendered_status" data-render="status">Status</th>
            <th data-index="worker_name">Current worker</th>
        </tr>
        </thead>
    </table>
    {{--</div>--}}

    {{-- FAB --}}
    <!-- <div class="fixed-action-btn">
        <a href="#create-order" class="btn-floating btn-large pink waves-effect modal-trigger">
            <i class="large material-icons">add</i>
        </a>
    </div>

    <div id="create-order" class="modal">
        <div class="modal-content">
            <h4>Create order</h4>
            <form class="jvalidate-form">
                Sorry, not ready yet. Ask your clients to create an order on their own :-)
            </form>
        </div>
        <div class="modal-footer">
            <a class="modal-action waves-effect waves-green btn">Create</a>
        </div>
    </div> -->

@endsection

@push('scripts')
    <script type="text/javascript">
        CJMA.DI.get('datagrid').addGrid({
            statusRenderer: function (data, type, row) {
                if (type != 'display') {
                    return data;
                }
                var timeLeft = '-';
                if (row.due_at == '' || row.due_at == undefined) {
                    var hours = Math.floor(row.due_at / 60),
                        minutes = row.due_at % 60;
                    //an extravagant way to turn [5, 1] -> "05:01"
                    timeLeft = [hours, minutes].map(function (part) {
                        return part < 10 ? '0' + part : part;
                    }).join(':');
                }
                return data + '<br />' + timeLeft;
            },
            success: {
                cancel: "Order canceled. All workers were unassigned."
            },
            error: {
                cancel: "There was an error canceling order."
            },
            search: '#search',
            responsive: true
        });
    </script>

@endpush
