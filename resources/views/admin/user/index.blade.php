@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:user.index') }}" class="breadcrumb">Users</a>
    <a class="breadcrumb"> {{ ucfirst($role).'s' }}</a>
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
    <table class="centered striped">
        <thead>
        <tr>
            <th>First name</th>
            <th>Last name</th>
            <th>Mobile phone</th>

            <th>Actions</th>
        </tr>
        </thead>
    </table>

    {{-- FAB --}}
    <div class="fixed-action-btn">
        <a href="{{ route('admin:userCreate', ['role' => $role]) }}" class="btn-floating btn-large pink waves-effect">
            <i class="large material-icons">add</i>
        </a>
        <ul>
            <li><a href="{{ route('admin:userCreate', ['role' => 'administrator']) }}"
                   class="btn-floating teal lighten-3"><i
                            class="material-icons">perm_identity</i></a></li>
            <li><a href="{{ route('admin:userCreate', ['role' => 'sub-admin']) }}"
                   class="btn-floating teal lighten-3"><i
                            class="material-icons">supervisor_account</i></a></li>
            <li><a href="{{ route('admin:userCreate', ['role' => 'worker']) }}" class="btn-floating teal lighten-3"><i
                            class="material-icons">android</i></a></li>
            <li><a href="{{ route('admin:userCreate', ['role' => 'customer']) }}" class="btn-floating teal lighten-2"><i
                            class="material-icons">assignment_ind</i></a></li>
        </ul>
    </div>

@endsection

@push('scripts')

    <script>
                {{-- Datatable --}}
        var datatable = $('table').DataTable({
                processing: true,
                serverSide: true,
                bFilter: true,
                bLengthChange: false,
                ajax: '/getUsers/{{ $role }}',
                columns: [
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'mobile_phone', name: 'mobile_phone'},

                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

        {{-- Delete model --}}
        function deleteModel(modelId) {
            if (!isNaN(modelId)) {
                modelId = +modelId;
            }

            $.ajax({
                url: '/user/' + modelId,
                type: 'DELETE',
                data: {id: modelId}
            }).done(function (response) {
                datatable.ajax.reload(null, false);
            }).fail(function (response) {
                Materialize.toast('Error', 4000);
            });
        }

        {{-- Search --}}
        $('#search').keyup(function () {
            datatable.search($(this).val()).draw();
        })
    </script>

@endpush