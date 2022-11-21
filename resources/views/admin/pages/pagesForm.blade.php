<?php
$isNew = isset($page->id) ? false : true;
?>
@extends('layouts.page')

@section('breadcrumb')

    <a href="{{ route('admin:pages') }}" class="breadcrumb">Pages</a>
    <a class="breadcrumb">{!! ($isNew) ? 'Create' : 'Update' !!}</a>

@endsection

@section('content')

    {!! Form::open([
    'class' => 'form jvalidate-form',
    ]) !!}
    <div class="row">
        <div class="input-field col l3 m4 s8 offset-l2 offset-m1 offset-s2">
            <input id="page_name" @if(!$isNew) value="{{$page->page_name}}" @endif name="page_name">
            <label for="page_name" class="active">Page Name</label>
        </div>
        <div class="input-field col l3 m4 s8 offset-l2 offset-m1 offset-s2">
            <input id="page_slug" @if(!$isNew) value="{{$page->page_slug}}" readonly @endif name="page_slug">
            <label for="page_slug" class="active">Page Slug</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col l8 m9 s8 offset-l2 offset-m1 offset-s2">
            <textarea name="page_content" id="editor">{!! (!$isNew) ? $page->page_content : '' !!}</textarea>
        </div>
    </div>
    <div class="row">
        <div class="input-field col l8 m9 s8 offset-l2 offset-m1 offset-s2">
            <button class="btn" type="submit" style="float: right;">@if(!$isNew) Update @else Create @endif</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
<script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>

@push('scripts')
    <script>
        var editor = CKEDITOR.replace('editor', {
            height: 300,
        });
        editor.on('change', function (evt) {
            $('textarea[name="page_content"]').val(evt.editor.getData())
        });

        CJMA.DI.get('form').addForm({
            form: '.form',
            errorMessage: 'Something went wrong',
            successUrl: '{!! route('admin:pages') !!}',
            baseUrl: '{!! route('admin:pages.store', ['id' => ($isNew) ? false : $page->id]) !!}'
        });
    </script>
@endpush