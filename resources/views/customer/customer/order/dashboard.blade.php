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
            <h1 class="m-n font-thin h2">My Orders</h1>
        </div>

        <div class="row wrapper">
            <div class="btn-group pull-left" role="group">
                <a href="{{ route('customer:createOrder') }}#/order/create" class="btn btn-primary">Create order</a>
            </div>
        </div>

        <div class="row wrapper">
            <div class="panel panel-default">
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light custom-table orders"
                           data-source="{{ route('customer:getOrdersData') }}" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th data-priority="1">Address</th>
                            <th>Time Left</th>
                            <th>Type</th>
                            <th data-priority="3">Status</th>
                            <th>Payment</th>
                            <th data-priority="2">Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div id="modal-comment-customer" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <div id="title"></div>
                        </div>
                        <input id="isComment" type="hidden" value="true">
                        <input id="isRework" type="hidden" value="true">
                        <form id="comment_form" action="{{ route('customer:invitation.reworking') }}" method="POST" onsubmit="event.preventDefault();" class="comment-form">
                        <div class="modal-body form-group">
                            <textarea class="form-control" rows="4" cols="20" name="content" id="comment-content-main"></textarea>
                            <input id="orderId" type="hidden" name="orderId" value="">
                        </div>
                        <div class="modal-footer">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary pull-right">Send Back</button>
                            </div>
                        </div>
                        </form>
                        <form id="comment_attach_form" action="{{$uploadConfig['uploadUrl']}}" method="POST" class="cors-form" enctype="multipart/form-data" style="display:none" onsubmit="event.preventDefault();">
                            <div class="modal-body form-group">
                                <div class="col s12">
                                    <textarea rows="4" cols="20" class="form-control" id="comment-content"></textarea>
                                    <input type="hidden" id="comment-order" value="">
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
                            <p style="margin-top:-70px; margin-left:10px"><input type="checkbox" id="attachent-check" /><label for="attachent-check"><span class="ui"></span>ADD ATTACHMENT<br>
                            Note: There is no need to resend your deliverables</label></p>
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

    $('#modal-comment-customer').on('shown.bs.modal', function(e) {
        var orderId = $(e.relatedTarget).attr('data-id');
        var OrderTitle = $(e.relatedTarget).attr('data-title');
        $("#orderId").val(orderId);
        $("#comment-order").val(orderId);
        document.getElementById("title").innerHTML = '<h4 id="modalCommentTitle" class="modal-title">Order: "'+OrderTitle+'", What is wrong?</h4>'
    });

    CJMA.DI.get('form').addForm({
        form: '.comment-form',
        errorMessage: 'Failed',
        successUrl: '/dashboard'
    });

     function deliver(order_id) {
        warnBeforeRedirect(order_id);
     };

     function warnBeforeRedirect(order_id) {
        if (confirm('Are you sure you want to mark this order as completed?')) {    
            var url = "{{ route('customer:order.complete', ['orderId' => ':order_id']) }}";
            url = url.replace(':order_id', order_id);                    
            window.location.href = url;
        }
     };

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