@extends('layouts.entity')

@section('breadcrumb')

    <a href="{{ route('admin:comment.index') }}" class="breadcrumb">Comments</a>
    <a class="breadcrumb">@if(isset($comment)) Edit {{ $comment->id }} @else Create @endif</a>

@endsection

@section('content')
    <form action="{{ route('admin:comment.update', ['comment_id' => $comment->id]) }}" method="PUT" class="ajax-form" onsubmit="event.preventDefault();">
        <div class="row">
            <div class="centered input-field col s12">
              <textarea name="content" id="textarea1" class="materialize-textarea" required>{{ $comment->content }}</textarea>
              <label for="textarea1">Comment</label>
            </div>
          </div>
        <div class="col s12 right-align action-btn-margin">
            <button type="submit" class="waves-effect btn">Save</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    CJMA.DI.get('form').addForm({
        form: '.ajax-form',
        method: "PUT",
        errorMessage: 'Failed',
        successUrl: "{{ route('admin:comment.index')  }}"
    });
</script>

@endpush