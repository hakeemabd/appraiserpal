@extends('layouts.page', ['breadcumb' => false, 'search' => false])
<?php
$i = 0;
?>
@section('content')
    <div class="col s12 m8 l12">
        <div class="card">
            <div class="card-content">
                @if ($i == 0)
                    <p>No messages available</p>
                @else
                    <p>No new invitations</p>
                @endif
            </div>

        </div>
    </div>
</div>


    <!-- New Task Form -->
    <form action="/task" method="POST" class="form-horizontal" style="width: 96%; padding-left: 1rem">
        {{ csrf_field() }}

        <!-- Task Name -->
        <div class="col s12 m8 l12">
        <div class="card" >
            <div class="card-content">
                <div class="form-floating">
                    <textarea class="form-control" placeholder="Type your message here..." id="floatingTextarea2" style="height: 100px"></textarea>
                </div>
            </div>
        </div>
        </div>

        <!-- Add Task Button -->
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
