@extends('layouts.page', ['breadcumb' => false, 'search' => false])

<?php
function getOrderFirstImageUrl($invitation)
{
    $image = $invitation->order->firstImage()->first();
    if ($image) {
        return $image->getImageUrl(\App\Models\Attachment::IMG_SMALL);
    }
    else {
        return \App\Models\Attachment::DEFAULT_IMAGE;
    }
}
?>

@section('content')

    <div class="col s12 m8 l9">
        <div class="card">
            <div class="card-content">
                @if(sizeof($invitations) > 0)
                    <span class="card-title">New invitations</span>
                    <ul class="collection">
                        @foreach($invitations as $invitation)
                            <li class="collection-item avatar">
                                <img src="{{ getOrderFirstImageUrl($invitation) }}" alt="" class="circle">
                                <span class="title"><a href="{{ route('worker:order.show', ['order' => $invitation->order]) }}">{{ $invitation->order->title }}</a></span>
                                <p> You have <strong>{{ $invitation->getTurnAroundTime()}}</strong> to complete task as <strong>{{ $invitation->group->name }}</strong></p>
                                <span class="secondary-content">
                                    <a href="{{ route('worker:invitation.reject', ['code' => $invitation->code]) }}"
                                       class="tooltiped" data-tooltip="Reject invitation"><i
                                            class="material-icons">highlight_off</i></a>
                                    <a href="{{ route('worker:invitation.accept', ['code' => $invitation->code]) }}"
                                       class="tooltiped" data-tooltip="Accept invitation"><i
                                            class="material-icons">send</i></a>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No new invitations</p>
                @endif
            </div>

        </div>
    </div>
    <div class="col s12 m4 l3">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Availability</span>
                <div class="switch">
                    <label>
                        Off
                        <input data-source="/availability" id="availability" type="checkbox"
                               @if(Sentinel::check()->available) checked=""@endif>
                        <span class="lever"></span>
                        On
                    </label>
                </div>
            </div>
        </div>

    </div>

@endsection