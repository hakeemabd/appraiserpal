@extends('layout.page', ['breadcumb' => false, 'search' => false])
<?php
$i = 0;
?>
@section('content')
    <div class="col s12 m8 l12">
        <div class="card">
            <div class="card-content">
                @if ($i == 0)
                    <p>New Message Available</p>
                @else
                    <p>No new invitations</p>
                @endif
            </div>

        </div>
    </div>
    </div>


    <!-- New Task Form -->
    <form action='{{ route('customer:messages.store') }}' method="POST" enctype="multipart/form-data" class="form-horizontal" style="width: 96%; padding-left: 1rem">
        {{ csrf_field() }}

        <!-- Message -->
        <div class="col s12 m8 l12">
            <div class="card">
                <div class="card-content">
                    <div class="form-floating">
                        <textarea class="form-control" name="text"  placeholder="Type your message here..."  style="height: 100px"></textarea>
                    
                    </div>
                </div>
            </div>
        </div>

        <!-- Send Message -->
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-default">
                    <i class="fa fa-plus"></i> Send Message
                </button>
            </div>
        </div>
        </br>
        </br>
        </br>
    </form>
@endsection
