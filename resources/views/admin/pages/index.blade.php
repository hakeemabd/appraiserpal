@extends('layouts.page')
@section('breadcrumb')
    <a href="{{ route('admin:pages') }}" class="breadcrumb">Pages</a>
    <form class="right">
        <div class="input-field">
            <input id="search" type="search" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>
@endsection
@section('content')
    {{--data-source="{{ route('admin:order.data') }}" datagrid--}}
    <table class="centered datagrid" data-source="{{ route('admin:pages.data') }}">
        <thead>
        <tr>
            <th>ID</th>
            <th data-index="page_name">Page Name</th>
            <th data-index="page_slug">Page Slug</th>
        </tr>
        </thead>
    </table>
    {{--<div class="fixed-action-btn">--}}
        {{--<a href="{{ route('admin:pages.addnew') }}" class="btn-floating btn-large pink waves-effect">--}}
            {{--<i class="large material-icons">add</i>--}}
        {{--</a>--}}
    {{--</div>--}}
@endsection

@push('scripts')
    <script type="text/javascript">
        CJMA.DI.get('datagrid').addGrid({
            search: '#search',
            responsive: true
        });
    </script>

@endpush
