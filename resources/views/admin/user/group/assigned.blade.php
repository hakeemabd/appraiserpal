@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:workerGroup.index') }}" class="breadcrumb">Worker Groups</a>
    <a class="breadcrumb">{{ $model->name }} workers</a>

@endsection

@section('content')

    {{-- Datatable --}}
    <table class="centered striped assigned-users datagrid"
           data-source="{{ route('admin:workerGroup.assignedData', ['group' => $model]) }}">
        <thead>
        <tr>
            <th data-index="last_name" data-render="fullName">Name</th>
            <th data-render="money">Fee</th>
            <th data-index="second_fee" data-render="money">Secondary fee</th>
            <th data-index="first_turnaround" data-render="timeFromMinutes">First turnaround</th>
            <th data-index="next_turnaround" data-render="timeFromMinutes">Next turnarounds</th>
        </tr>
        </thead>
    </table>

    {{-- FAB --}}
    <div class="fixed-action-btn">
        <a href="#assign-modal" class="btn-floating btn-large pink waves-effect modal-trigger">
            <i class="large material-icons">link</i>
        </a>
    </div>

    <div id="assign-modal" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4>Assign users</h4>
            <form class="group-assignment jvalidate-form"
                  action="{{ route('admin:workerGroup.assign', ['group' => $model]) }}">
                {!! Form::materialCollectionSelect("user_id", $workers, null, ['label' => 'Worker', 'val' => 'fullName']) !!}
                {!! Form::materialText("fee", '', null) !!}
                {!! Form::materialText("second_fee", '', null) !!}
                {!! Form::materialText("first_turnaround", '', null) !!}
                {!! Form::materialText("next_turnaround", '', null) !!}
            </form>
        </div>
        <div class="modal-footer">
            <a class="modal-action waves-effect waves-green btn">Assign</a>
        </div>
    </div>

    <div id="assign-edit-modal" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4>Edit assignment - <span></span></h4>
            <form class="group-assignment jvalidate-form"
                  action="{{ route('admin:workerGroup.assign', ['group' => $model]) }}">
                {!! Form::hidden("user_id", null) !!}
                {!! Form::materialText("fee", '', null) !!}
                {!! Form::materialText("second_fee", '', null) !!}
                {!! Form::materialText("first_turnaround", '', null) !!}
                {!! Form::materialText("next_turnaround", '', null) !!}
            </form>
        </div>
        <div class="modal-footer">
            <a class="modal-action waves-effect waves-green btn">Save</a>
        </div>
    </div>
@endsection