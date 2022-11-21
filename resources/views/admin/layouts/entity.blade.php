@extends('layouts.page', ['search' => false])

@section('content')

    <form class="entity-form jvalidate-form row">

        @yield('form-content')

        <div class="col s12 center-align action-btn-margin">
            <button type="submit" class="waves-effect btn">Save</button>
        </div>

    </form>

@endsection