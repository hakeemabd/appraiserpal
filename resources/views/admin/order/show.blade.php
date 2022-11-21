<?php
use App\Models\Attachment;

$attachmentMeta = [
    Attachment::TYPE_MC1004 => '1004 MC Export File',
    Attachment::TYPE_MLS => 'MLS Sheets',
    Attachment::TYPE_SKETCH => 'Sketches',
    Attachment::TYPE_SAMPLE => 'Sample file',
    Attachment::TYPE_ADJ_SHEETS => 'Adjustment sheets',
    Attachment::TYPE_CLONE => 'Clone file'
];

if (isset($attachments[Attachment::TYPE_DATA_MOBILE])) {
    $attachmentMeta[Attachment::TYPE_DATA_MOBILE] = 'Primary pdf';
} else {
    $attachmentMeta[Attachment::TYPE_DATA_MANUAL] = 'Custom Forms';
}

$instructions = [
    'standard_instructions' => 'Standard Instructions',
    'specific_instructions' => 'Specific Instructions',
];

$generalInfo = [
    'title' => 'Order title',
    'effective_date' => 'Effective date',
    'reportType' => 'Category',
    'assignment_type' => 'Assignment Type',
    'financing' => 'Financing',
    'occupancy_type' => 'Occupancy',
    'property_rights' => 'Property rights',
];

$overview = [
    'title' => 'Order Title',
    'reportType' => 'Order Category',
    'orderTimeLeft' => 'Time Left',
    'customerStatus' => "Status"
];

?>


@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:order.index') }}" class="breadcrumb">Orders</a>
    <a class="breadcrumb">{{ $order->title }}</a>
    <div class="right">
        <a class='btn' href='#'>Split view</a>
        <a class='dropdown-button btn' href='#' data-activates='actions'>Actions</a>
        <ul id='actions' class='dropdown-content'>
            <li><a href="#!">Edit</a></li>
            <li><a href="#!">Remove</a></li>
            <li><a href="#!">Order Settings</a></li>
            <li><a href="{{route('admin:order.assignments.index', ['order' => $order])}}">Invite Workers</a></li>
        </ul>
    </div>
@endsection
<style type="text/css">
    #attachent-check:not(:checked),
    #attachent-check:checked {
        position: absolute;
        left: -9999px;
    }

    #attachent-check:not(:checked) + label,
    #attachent-check:checked + label {
        position: relative;
        padding-left: 75px;
        cursor: pointer;
    }

    #attachent-check:not(:checked) + label:before,
    #attachent-check:checked + label:before,
    #attachent-check:not(:checked) + label:after,
    #attachent-check:checked + label:after {
        content: '';
        position: absolute;
    }

    #attachent-check:not(:checked) + label:before,
    #attachent-check:checked + label:before {
        left: 0;
        top: -3px;
        width: 65px;
        height: 30px;
        background: #DDDDDD;
        border-radius: 15px;
        transition: background-color .2s;
    }

    #attachent-check:not(:checked) + label:after,
    #attachent-check:checked + label:after {
        width: 20px;
        height: 20px;
        transition: all .2s;
        border-radius: 50%;
        background: #7F8C9A;
        top: 2px;
        left: 5px;
    }

    /* on checked */
    #attachent-check:checked + label:before {
        background: #34495E;
        left: 0;
        top: -3px;
        width: 65px;
        height: 30px;
        border-radius: 15px;
        transition: background-color .2s;
        transform: rotate(0deg);
    }

    #attachent-check:checked + label:after {
        background: #39D2B4;
        top: 2px;
        left: 60px;
    }

    #attachent-check:checked + label .ui,
    #attachent-check:not(:checked) + label .ui:before,
    #attachent-check:checked + label .ui:after {
        position: absolute;
        left: 6px;
        width: 65px;
        border-radius: 50%;
        font-size: 14px;
        font-weight: bold;
        line-height: 22px;
        transition: all .2s;
    }

    #attachent-check:not(:checked) + label .ui:before {
        content: "no";
        left: 32px
    }

    #attachent-check:checked + label .ui:after {
        content: "yes";
        color: #39D2B4;
    }

    #attachent-check:focus + label:before {
        border: 1px dashed #777;
        box-sizing: border-box;
        margin-top: -1px;
    }
</style>
@section('content')
    <div class="row">
        <div class="col s12">
            <ul class="tabs">
                <li class="tab col s3"><a href="#info">Info</a></li>
                <li class="tab col s3"><a href="#docs">Documents</a></li>
                <li class="tab col s3"><a href="#history">Tracking</a></li>
            </ul>
        </div>
        <div id="info" class="col s12 m12 l12">
            <h4 class="center-align">{{ $order->title }}</h4>
            <div class="row">
                <div class="col s12">
                    <div class="card card-info">
                        <div class="card-content">
                            <div class="card-title">
                                <h5 class="m-n font-thin h4">General Information</h5>
                            </div>
                            <div class="row">
                                <?php $line = 1?>
                                @foreach ($generalInfo as $type => $title)
                                    <div class="col s12 m6 no-padding">
                                        <div class="row">
                                            <div class="col s4 no-padding">
                                                <b>{{ $title }}:</b>
                                            </div>
                                            <div class="col s8 no-padding">
                                                {{ ($type != 'reportType') ? $order[$type] : $order[$type]['name'] }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($line % 7 == 0 )
                                        <div class="fix-clear"></div>
                                    @endif
                                    <?php $line++?>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-title">
                                <h5 class="m-n font-thin h4">Images</h5>
                            </div>
                            @if(isset($attachments[Attachment::TYPE_PHOTO]))
                                @include('widgets.swiperSlider', [
                                    'slides' => $attachments[Attachment::TYPE_PHOTO],
                                    'id' => 'photos'
                                ])
                            @else
                                <div>
                                    No photos added
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($instructions as $type => $title)
                <div class="row">
                    <div class="col s12">
                        <div class="card card-info">
                            <div class="card-content">
                                <div class="card-title">
                                    <h5 class="m-n font-thin h4">{{ $title }}</h5>
                                </div>
                                <p>
                                    @if($order[$type])
                                        {{ $order[$type] }}
                                    @else
                                        No added
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5>Comparables</h5>
                            @if(isset($attachments[Attachment::TYPE_COMPARABLE]))
                                @include('widgets.swiperSlider', [
                                    'slides' => $attachments[Attachment::TYPE_COMPARABLE],
                                    'id' => 'comparables'
                                ])
                            @else
                                <div>
                                    No photos added
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5 class="m-n font-thin h4">FeedBack</h5>
                            <div class="row">
                                <div class="col s12 m7 l9">
                                    <div class="card">
                                        <div class="card-content">
                                            @if(isset($order->feedback) && strlen($order->feedback))
                                                <p>{{$order->feedback}}</p>
                                            @else
                                                <p>No feedback added</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col s12 m5 l3">
                                    <div class="card">
                                        <div class="card-content center-align">
                                            <?php $rating = (int)$order->feedbackRating;?>
                                            @for($i = 0; $i < 5; $i++)
                                                <i class="material-icons <?=($i < $rating) ? "yellow-text " : "grey-text"?>">
                                                    grade
                                                </i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5 class="m-n font-thin h4">Deliverables</h5>
                            <table id="upload_final_documents_datatable" class="centered striped datagrid"
                                   data-source="{{route('admin:documents.final.files', ['orderId' => $order->id])}}">
                                <thead>
                                <tr>
                                    {{--<th data-index="id" data-render="">Id</th>--}}
                                    <th data-index="col_file_label">File</th>
                                    <th data-index="col_editor_name">Worker's Name</th>
                                    <th data-index="col_file_name">Name</th>
                                    <th data-index="col_is_approved">Is Approved</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5 class="m-n font-thin h4">Comments</h5>
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s3"><a id="tab-publicInfo" class="active" href="#publicInfo">Public</a>
                                        </li>
                                        <li class="tab col s3"><a id="tab-privateInfo" href="#privateInfo">Private</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="publicInfo" class="col s12">
                                    <table class="centered striped datagrid"
                                           data-source="{{route('admin:comments', ['orderId' => $order->id, 'namespace' => 'public'])}}"
                                           data-action="0">
                                        <thead>
                                        <tr>
                                            <th data-index="col_comment_file"></th>
                                            <th data-index="col_comment_data"></th>
                                            <th data-index="col_comment_content"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div id="privateInfo" class="col s12">
                                    <table class="centered striped datagrid"
                                           data-source="{{route('admin:comments', ['orderId' => $order->id, 'namespace' => 'private'])}}"
                                           data-action="0">
                                        <thead>
                                        <tr>
                                            <th data-index="col_comment_file"></th>
                                            <th data-index="col_comment_data"></th>
                                            <th data-index="col_comment_content"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="docs" class="col s12">
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5>Overview</h5>
                            <table class="striped">
                                <tr>
                                    <td>Customer</td>
                                    <td>
                                        <a href="{{route('admin:user.show', ['id' => $user->getId()])}}">{{$user->getFullNameAttribute()}}</a>
                                    </td>
                                </tr>
                                @foreach ($overview as $type => $title)
                                    <tr>
                                        <td>{{$title}}</td>
                                        @if ($type !== 'orderTimeLeft')
                                            <td>{{ ($type != 'reportType') ? $order[$type] : $order[$type]['name'] }}</td>
                                        @else
                                            <td>
                                                @lang($order[$type])
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5>Initial Documents</h5>
                            <table id="initial_documents_datatable" class="centered striped datagrid"
                                   data-source="{{route('admin:documents.files', ['orderId' => $order->id])}}">
                                <thead>
                                <tr>
                                    <th data-index="type">File Type</th>
                                    <th data-index="name">Name</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5>Upload Documents</h5>
                            <table id="upload_documents_datatable" class="centered striped datagrid"
                                   data-source="{{route('admin:documents.uploaded.files', ['orderId' => $order->id])}}">
                                <thead>
                                <tr>
                                    {{--<th data-index="id" data-render="">Id</th>--}}
                                    <th data-index="col_file_label">File</th>
                                    <th data-index="col_editor_name">Worker's Name</th>
                                    <th data-index="col_file_name">Name</th>
                                    <th data-index="col_file_is_final">Is Final</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <a class="waves-effect waves-light btn modal-trigger" href="#modal-upload">Upload</a>
                </div>
                <div id="modal-upload" class="modal">
                    <div class="modal-content">
                        <h4>Upload file</h4>
                        <form class="cors-form" action="{{$uploadConfig['uploadUrl']}}" method="post"
                              enctype="multipart/form-data" onsubmit="event.preventDefault();">

                            <input type="hidden" name="key">
                            <input type="hidden" name="AWSAccessKeyId" value="{{$uploadConfig['accessKey']}}">
                            <input type="hidden" name="acl" value="private">
                            <input type="hidden" name="policy"
                                   value="{{$uploadConfig['uploadConfig'][$configType]['policy']}}">
                            <input type="hidden" name="signature"
                                   value="{{$uploadConfig['uploadConfig'][$configType]['signature']}}">
                            <input type="hidden" name="Content-Type">
                            <input type="hidden" name="success_action_status"
                                   value="{{$uploadConfig['uploadConfig'][$configType]['success_action_status']}}">

                            <div class="row">
                                <div class="input-field col s12">
                                    <input value="" type="text" id="label-id" class="validate" required>
                                    <label for="disabled">Set title for you file</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="file-field input-field col s12">
                                    <div class="btn">
                                        <span>File</span>
                                        <input type="file" name="file">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect btn">Upload</button>
                                    <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h5 class="m-n font-thin h4">Comments</h5>
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s3"><a id="tab-public" class="active"
                                                                  href="#public">Public</a></li>
                                        <li class="tab col s3"><a id="tab-private" href="#private">Private</a></li>
                                    </ul>
                                </div>
                                <div id="public" class="col s12">
                                    <table id="public_comment_datatable" class="centered striped datagrid"
                                           data-source="{{route('admin:comments', ['orderId' => $order->id, 'namespace' => 'public'])}}"
                                           data-action="0">
                                        <thead>
                                        <tr>
                                            <th data-index="col_comment_file"></th>
                                            <th data-index="col_comment_data"></th>
                                            <th data-index="col_comment_content"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div id="private" class="col s12">
                                    <table id="private_comment_datatable" class="centered striped datagrid"
                                           data-source="{{route('admin:comments', ['orderId' => $order->id, 'namespace' => 'private'])}}"
                                           data-action="0">
                                        <thead>
                                        <tr>
                                            <th data-index="col_comment_file"></th>
                                            <th data-index="col_comment_data"></th>
                                            <th data-index="col_comment_content"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12" id="comments">
                    <a class="waves-effect waves-light btn modal-trigger" href="#modal-comment">Add Comment</a>
                </div>
                <input id="isComment" type="hidden" value="false">
                <input id="isRework" type="hidden" value="false">
                <div id="modal-comment" class="modal">
                    <div class="modal-content">
                        <div id="modalCommentTitle"></div>
                        <form id="comment_form" action="{{ route('admin:comments') }}" method="POST"
                              class="comment-form" style="display:block" onsubmit="event.preventDefault();">
                            <div class="row form-group">
                                <div class="row">
                                    <div class="col s12">
                                        <textarea rows="4" cols="20" name="content" class="texta-comments"
                                                  id="comment-content-main"></textarea>
                                        <input type="hidden" name="orderId" value="{{$order->id}}">
                                        <input id="inputNamespace" type="hidden" name="namespace" value="public">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 right-align">
                                        <button type="submit" class="waves-effect btn">SAVE</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form id="comment_attach_form" action="{{$uploadConfig['uploadUrl']}}" method="POST"
                              class="cors-form" enctype="multipart/form-data" style="display:none"
                              onsubmit="event.preventDefault();">
                            <div class="row form-group">
                                <div class="row">
                                    <div class="col s12">
                                        <textarea rows="4" cols="20" class="texta-comments"
                                                  id="comment-content"></textarea>
                                        <input type="hidden" id="comment-order" value="{{$order->id}}">
                                        <input id="inputNamespace" type="hidden" value="public">
                                    </div>
                                </div>
                                <div class="row">
                                    <div>
                                        <input type="hidden" name="key">
                                        <input type="hidden" name="AWSAccessKeyId"
                                               value="{{$uploadConfig['accessKey']}}">
                                        <input type="hidden" name="acl" value="private">
                                        <input type="hidden" name="policy"
                                               value="{{$uploadConfig['uploadConfig'][$configType]['policy']}}">
                                        <input type="hidden" name="signature"
                                               value="{{$uploadConfig['uploadConfig'][$configType]['signature']}}">
                                        <input type="hidden" name="Content-Type">
                                        <input type="hidden" name="success_action_status"
                                               value="{{$uploadConfig['uploadConfig'][$configType]['success_action_status']}}">

                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input value="" type="text" id="label-id-2" class="validate" required>
                                                <label for="disabled">Set title for you file</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="file-field input-field col s12">
                                                <div class="btn">
                                                    <span>File</span>
                                                    <input type="file" name="file">
                                                </div>
                                                <div class="file-path-wrapper">
                                                    <input class="file-path validate" type="text" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col s12 right-align">
                                        <button type="submit" class="waves-effect btn">SAVE</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col s6 leaft-align">
                            <p style="margin-top:-70px"><input type="checkbox" id="attachent-check"/><label
                                        for="attachent-check"><span class="ui"></span>ADD ATTACHMENT</label></p>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            @if($btnType == 'complete')
                <div id="modal-group" class="modal">
                    <div class="modal-content">
                        <h4>Sent to group</h4>
                        <form class="ajax-form"
                              action="{{route('admin:invitation.reworking', ['orderId' => $order->id])}}" method="post"
                              onsubmit="event.preventDefault();">
                            <div class="row">
                                <div class="input-field col s12">
                                    {!! Form::select('group_id', $reviewerData) !!}
                                    <label for="disabled"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect btn">Send</button>
                                    <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col s12">
                    <div class="row right-align">
                        <div class="col s12">
                            @if ($order->is_completed === 0 && $markComplete)
                                <button class='dropdown-button btn' data-activates='order-actions'>Actions</button>
                                <ul id='order-actions' class='dropdown-content'>
                                    <li><a href="{{ route('admin:order.complete', ['orderId' => $order->id]) }}">Mark as
                                            Completed</a></li>
                                    <li><a class="modal-trigger" href="#modal-group">Send to GroupName</a></li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($btnType == 'finish')
                <div class="col s12">
                    <div class="row right-align">
                        <div class="col s12">
                            <button class='dropdown-button btn' data-activates='order-actions'>Actions</button>
                            <ul id='order-actions' class='dropdown-content'>
                                <li>
                                    <a href="{{ route('worker:invitation.finish', ['orderId' => $order->id]) }}">Finish</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <div id="history" class="col s12">
            <div class="row">
                <div class="col s12">
                    <a class="waves-effect waves-light btn modal-trigger" href="#additional-time-modal">Add time</a>
                </div>
            </div>
            <table class="centered striped datagrid" data-source="{{ route('admin:history', ['order' => $order->id]) }}"
                   data-action="0">
                <thead>
                <tr>
                    <th data-index="col_log_created_at">Date</th>
                    <th data-index="col_log_description">Description</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="additional-time-modal" class="modal">
        <div class="modal-content">
            <h4>Add more time (hours)</h4>
            <form action="{{ route('admin:order.add.time', ['orderId' => $order->id]) }}" method="post"
                  class="time-form" onsubmit="event.preventDefault();">
                <div class="row">
                    <div class="input-field col s12">
                        <input id="additional-time-id" value="" type="number" class="validate" name="time" required>
                        <label for="additional-time-id">Set time</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <button type="submit" class="waves-effect btn">Add</button>
                        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript">
        document.getElementById('modalCommentTitle').innerHTML = '<h4>Add Comment (Public)</h4>';
        var datatables = null;

        $('.tabs li a[href="#docs"]').click(function () {
            if (!$(this).data('click')) {
                $(this).attr('data-click', 'true');
                for (var i in datatables) {
                    datatables[i].destroy();
                }
                CJMA.DI.get('datagrid').addGrid({responsive: true, actions: true});
            }
        });

        CJMA.DI.get('corsForm').addForm({
            folder: "{{$uploadConfig['uploadConfig'][$configType]['folder']}}",
            configType: "{{$configType}}",
            method: "POST",
            baseUrl: $('.cors-form').attr('action'),
            serverUrl: "/attachment/{{$configType}}",
            extraFields: [
                {
                    name: 'label',
                    value: $('#label-id')
                },
                {
                    name: 'label2',
                    value: $('#label-id-2')
                },
                {
                    name: 'content',
                    value: $('#comment-content')
                },
                {
                    name: 'orderId',
                    value: $('#comment-order')
                },
                {
                    name: 'namespace',
                    value: $('#inputNamespace')
                },
                {
                    name: 'is_comment',
                    value: $('#isComment')
                },
                {
                    name: 'is_rework',
                    value: $('#isRework')
                }
            ]
        });

        CJMA.DI.get('form').addForm({
            errorMessage: 'Failed',
            successUrl: '/order/{{$order->id}}'
        });

        CJMA.DI.get('form').addForm({
            form: '.comment-form',
            errorMessage: 'Failed',
            successUrl: '/order/{{$order->id}}'
        });

        CJMA.DI.get('form').addForm({
            form: '.time-form',
            errorMessage: 'Failed',
            successUrl: '/order/{{$order->id}}'
        });

        $('#tab-public').on('click', function () {
            document.getElementById('inputNamespace').value = 'public';
            document.getElementById('modalCommentTitle').innerHTML = '<h4>Add Comment (Public)</h4>';
        });

        $('#tab-private').on('click', function () {
            document.getElementById('inputNamespace').value = 'private';
            document.getElementById('modalCommentTitle').innerHTML = '<h4>Add Comment (Private)</h4>';
        });

        $('#attachent-check').on('change', function () {
            var attachment = document.getElementById('attachent-check').checked;
            var isComment = document.getElementById('isComment');
            var commentForm = document.getElementById('comment_form');
            var commentAttachForm = document.getElementById('comment_attach_form');
            var mainContent = document.getElementById('comment-content-main');
            var content = document.getElementById('comment-content');
            if (!attachment) {
                commentForm.style.display = 'block';
                isComment.value = "false";
                commentAttachForm.style.display = 'none';
                mainContent.value = content.value;
            } else {
                commentForm.style.display = 'none';
                isComment.value = "true";
                commentAttachForm.style.display = 'block';
                content.value = mainContent.value;
            }
            ;
        });
    </script>
@endpush
