<?php
$profileInfo = [
        'Name' => $user->fullName,
        'License #' => $user['license_number'],
        'Cell Phone' => $user['mobile_phone'],
        'Work Phone' => $user['work_phone'],
        'Address' => (!empty($user['address_line_1']) ? $user['address_line_1'] . ' ' : '') .
                (!empty($user['address_line_2']) ? $user['address_line_2'] . ', ' : '') .
                (!empty($user['city']) ? $user['city'] . ' ' : '') .
                (!empty($user['state']) ? $user['state'] . ', ' : '') .
                (!empty($user['zip']) ? $user['zip'] : ''),
        'Standard Instruction' => $user['standard_instructions']
];
?>
@extends('layout.authorized')

@section('content')
    <div class="container">
        <!-- uiView:  -->
        <div class="app-content-body fade-in-up "><!-- uiView:  -->
            <div class="fade-in-down ">
                <div class="hbox hbox-auto-xs hbox-auto-sm ">
                    <div class="col">
                        @include('widgets.profileHeader', ['profile' => $user])
                        <div class="padder wrapper">
                            <div class="row">
                                @foreach($profileInfo as $fieldName => $fieldValue)
                                    <div class="panel panel-default">
                                        <div class="panel-heading">{{$fieldName}}</div>
                                        <div class="panel-body">
                                            {{!empty($fieldValue)?$fieldValue:'Please add your ' . $fieldName}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <a href="javascript:void(0);" id="editProfile" class="btn btn-primary pull-right">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden" id="editProfileTpl">
        @include('profile.editProfile', ['profile' => $user])
    </div>
@endsection