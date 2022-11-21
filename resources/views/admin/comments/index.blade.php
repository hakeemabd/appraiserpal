@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:comment.index') }}" class="breadcrumb">Comments</a>
    <form class="right">
        <div class="input-field">
            <input id="search" type="search" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>

@endsection

@section('content')
    <table class="centered stripe datagrid" data-source="{{ route('admin:comment.pending') }}">
        <thead>
        <tr>
            <th data-index="col_comment_worker">Worker</th>
            <th data-index="col_comment_order">Order Id</th>
            <th data-index="col_comment_content">Content</th>
        </tr>
        </thead>
    </table>

    <div id="comment-edit-modal" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4>Edit assignment - <span></span></h4>
            <form class="group-comment jvalidate-form"
                  action="{{ route('admin:comment.edit') }}">
                {!! Form::materialText("col_comment_content", '', null) !!}
            </form>
        </div>
        <div class="modal-footer">
            <a class="modal-action waves-effect waves-green btn">Save</a>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
CJMA.DI.get('datagrid').addGrid({
        search: '#search',
        responsive: true
    });
    
    /*document.cookie = "comments=0";
    document.getElementById("newComments").style.display = "none";*/
</script>

@endpush