@extends('layouts.page')

@section('breadcrumb')

    <a href="#" class="breadcrumb">Payments</a>
    <a class="breadcrumb">Promo Codes</a>

@endsection

@section('content')
    <div class="col s12">
        <table class="centered striped datagrid"
            data-source="{{ route('admin:promoCode.view') }}">
            <thead>
            <tr>
                <th>Id</th>
                <th>Code</th>
                <th>Percent</th>
                <th>Count</th>
            </tr>
            </thead>
        </table>
    </div>

    <div class="fixed-action-btn">
        <a href="{{ route('admin:promoCode') }}" class="btn-floating btn-large pink waves-effect">
            <i class="large material-icons">add</i>
        </a>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('datagrid').addGrid({
        success: {
            'delete': 'Promo Code successfully deleted'
        },
        error: {
            'delete': 'Promo Code is not deleted'
        }
    });
</script>
@endpush