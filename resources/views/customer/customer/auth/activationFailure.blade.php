@push('styles')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="{{ asset('build/styles/vendor.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('build/styles/app.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('header')
    <header class="un-conflict-wrapper">
        <div class="container">
            @include('landing.headerNav')
        </div>
    </header>
@endsection

@section('content')
    <div class="container">
        <h1>Error with your activation!</h1>
        <h3>{{ $error }}</h3>
        <a class="link" href="/">Home page</a>
    </div>
@endsection
