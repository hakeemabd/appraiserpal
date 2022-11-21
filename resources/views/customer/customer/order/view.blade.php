<?php
use App\Models\Attachment;

define('SYMBOL_IN_PREVIEW',800);

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

?>

@extends('layout.authorized')
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
  left:0; top: -3px;
  width: 65px; height: 30px;
  background: #DDDDDD;
  border-radius: 15px;
  transition: background-color .2s;
}
#attachent-check:not(:checked) + label:after,
#attachent-check:checked + label:after {
  width: 20px; height: 20px;
  transition: all .2s;
  border-radius: 50%;
  background: #7F8C9A;
  top: 2px; left: 5px;
}

/* on checked */
#attachent-check:checked + label:before {
  background:#34495E;
  left:0; top: -3px;
  width: 65px; height: 30px;
  border-radius: 15px;
  transition: background-color .2s;
}
#attachent-check:checked + label:after {
  background: #39D2B4;
  top: 2px; left: 40px;
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
    <div class="container">

        <div class="row wrapper">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1 class="m-n font-thin h2">Order {{ $order->id }}: {{ $order->title }}</h1>
            </div>
        </div>

        @if ($order->canEdit())
            <div class="row wrapper">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="btn-group pull-left" role="group">
                        <a href="{{ route('customer:order.edit', ['order' => $order]) }}#order/edit/{{ $order->id }}"
                           class="btn m-b-xs w-xs btn-primary">Edit order</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="row wrapper">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="panel panel-default panel-list">
                    <div class="panel-heading"><h2 class="m-n font-thin h4">General Information</h2></div>
                    <div class="panel-body">
                        @foreach ($generalInfo as $type => $title)
                            <p class="clearfix">
                                <b class="pull-left">{{ $title }}:</b>
                                <span
                                        class="pull-right">{{ ($type != 'reportType') ? $order[$type] : $order[$type]['name'] }}</span>
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>

            @foreach ($instructions as $type => $title)
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="panel panel-default panel-popup"
                         @if(SYMBOL_IN_PREVIEW < mb_strlen($order[$type]))
                            data-full="{{$order[$type]}}"
                         @endif
                    >
                        <div class="panel-heading"><h2 class="m-n font-thin h4">{{ $title }}</h2></div>
                        <div class="panel-body">
                            {{ substr($order[$type], 0, SYMBOL_IN_PREVIEW) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row wrapper">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><h2 class="m-n font-thin h4">Images</h2></div>
                    <div class="panel-body">
                        @if(isset($attachments[Attachment::TYPE_PHOTO]))
                            <div>
                                @include('widgets.swiperSlider', [
                                    'slides' => $attachments[Attachment::TYPE_PHOTO],
                                    'id' => 'photos'
                                ])
                            </div>
                        @else
                            <div>
                                No photos added
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row wrapper">
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h2 class="m-n font-thin h4">Attachments</h2></div>
                        <div class="panel-body">
                            <?php $colCounter = 0; ?>
                            @foreach ($attachmentMeta as $type => $title)
                                @if (isset($attachments[$type]))
                                    <?php $colCounter++; ?>
                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 wrapper">
                                        <h4 class="m-n font-thin h5">{{ $title }}</h4>
                                        <hr/>
                                        <ul class="link-list">
                                            @foreach ($attachments[$type] as $data)
                                                <li>
                                                    <a class="text-primary" target="_blank"
                                                       href="{{ $data['path'] }}">{{ $data->formatLabel() }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if($colCounter%3 == 0)
                                    <div class="clearfix"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h2 class="m-n font-thin h4">Comparables</h2></div>
                        <div class="panel-body">
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

            <div class="col-lg-12" id="download">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="m-n font-thin h4">Deliverables</h5>
                    </div>
                    <div class="panel-body table-responsive">
                        <table class="table">
                            <thead>
                                <th class="col-lg-5">File</th>
                                <th class="col-lg-5">Name</th>
                            </thead>
                            <tbody>
                                @foreach ($finalFiles as $file)
                                    @if ($file->approved === 1) 
                                    <tr>
                                        <td class="col-lg-4">{{ $file->getlabel() }}</td>
                                        <td class="col-lg-4"><a class="text-primary" href="{{ $file->path }}" target="_blank">{{ $file->name }}</a></td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
            <div class="row wrapper">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h2 class="m-n font-thin h4">Comments</h2></div>
                        <div class="panel-body table-responsive">
                        <table class="table">
                            <tbody>
                                @foreach ($comments as $comment)
                                    <tr>
                                        <td class="col-lg-1">
                                            @if (sizeof($comment->attachment) > 0)
                                                <div>{{$comment->attachment[0]->getlabel()}}</div><br>
                                                <a style="color: #5858FA" href="{{$comment->attachment[0]->path}}" target="_blank">{{$comment->attachment[0]->name}}</a>
                                            @else
                                                No attachment
                                            @endif
                                        </td>
                                        <td class="col-lg-3 center-block">
                                        @if ($comment->user->id !== $currentUser)
                                            @if ($comment->user->roles[0]->slug === 'worker')
                                                <div class="text-info">appraiser pal ({{$comment->user->id}})</div><br>{{ $comment->created_at->diffForHumans() }}
                                            @else
                                                <div class="text-info">{{ $comment->user->roles[0]->slug }}</div>{{$comment->user->first_name.' '.$comment->user->last_name }}<br>{{ $comment->created_at->diffForHumans() }}
                                            @endif
                                        @else
                                            <br><div class="text-primary">(You)</div>{{ $comment->created_at->diffForHumans()}}
                                        @endif
                                        </td>
                                        <td class="col-lg-8">
                                            <div class="card card-inverse card-primary text-xs-center" style="background-color: #dee5e7; border-color: #333;">
                                              <div class="card-block">
                                                <blockquote class="card-blockquote">
                                                  <p>{{ $comment->content }}</p>
                                                </blockquote>
                                              </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg12"  id="comments">
                    <a class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-comment-customer">Add Comment</a>
                </div>
                <input id="isComment" type="hidden" value="true">
                <input id="isRework" type="hidden" value="false">
                <div id="modal-comment-customer" class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 id="modalCommentTitle" class="modal-title">Add Comment</h4>
                            </div>
                                <form id="comment_form" action="{{ route('customer:comments') }}" method="POST" class="comment-form" style="display:block" onsubmit="event.preventDefault();">
                                    <div class="modal-body form-group">
                                        <div class="col s12">
                                            <textarea rows="4" cols="20" name="content" class="form-control" id="comment-content-main"></textarea>
                                            <input type="hidden" name="orderId" value="{{$order->id}}">
                                            <input id="inputNamespace" type="hidden" name="namespace" value="public">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col s12 right-align">
                                            <button type="submit" class="waves-effect btn">SAVE</button>
                                        </div>
                                    </div>
                                </form>
                                <form id="comment_attach_form" action="{{$uploadConfig['uploadUrl']}}" method="POST" class="cors-form" enctype="multipart/form-data" style="display:none" onsubmit="event.preventDefault();">
                                    <div class="modal-body form-group">
                                        <div class="col s12">
                                            <textarea rows="4" cols="20" class="form-control" id="comment-content"></textarea>
                                            <input type="hidden" id="comment-order" value="{{$order->id}}">
                                            <input id="inputNamespace" type="hidden" value="public">
                                        </div>
                                        <div>
                                            <input type="hidden" name="key">
                                            <input type="hidden" name="AWSAccessKeyId" value="{{$uploadConfig['accessKey']}}">
                                            <input type="hidden" name="acl" value="private">
                                            <input type="hidden" name="policy" value="{{$uploadConfig['uploadConfig'][$configType]['policy']}}">
                                            <input type="hidden" name="signature" value="{{$uploadConfig['uploadConfig'][$configType]['signature']}}">
                                            <input type="hidden" name="Content-Type">
                                            <input type="hidden" name="success_action_status" value="{{$uploadConfig['uploadConfig'][$configType]['success_action_status']}}">
                                            <br>
                                            <div class="input-field col s12">
                                                <input value="" type="text" id="label-id" class="validate" placeholder="Set title for you file" required>
                                            </div>
                                            <br>
                                            <div class="file-field input-field col s12">
                                                <div class="btn btn-primary" style="
                                                    position: relative;
                                                    overflow: hidden;
                                                    float:left">
                                                    <span>File</span>
                                                    <input type="file" name="file" style="position: absolute;
                                                        top: 0;
                                                        right: 0;
                                                        margin: 0;
                                                        padding: 0;
                                                        font-size: 20px;
                                                        cursor: pointer;
                                                        opacity: 0;
                                                        filter: alpha(opacity=0)">
                                                </div>
                                                <div class="file-path-wrapper">
                                                    <input class="file-path validate" type="text" style="margin-top:3px; margin-left:5px" disabled="true" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="pull-right">
                                            <button type="submit" class="waves-effect btn">SAVE</button>
                                        </div>
                                    </div>
                                </form>
                            <div class="form-group">
                                <p style="margin-top:-70px; margin-left:10px"><input type="checkbox" id="attachent-check" /><label for="attachent-check"><span class="ui"></span>ADD ATTACHMENT</label></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
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
        form: '.comment-form',
        errorMessage: 'Failed',
        successUrl: '/order/{{$order->id}}/show'
    });

    $('#attachent-check').on('change', function() {
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
        };
    });
</script>
@endpush