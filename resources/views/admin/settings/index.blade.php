@extends('layouts.page')

@section('content')

    <div class="col s12 m8 l12">
        <div class="card">
            <div class="card-content">
                @if(sizeof($settings) > 0)
                    <span class="card-title">Settings</span>
                    <form action="{{ route('admin:settings') }}" method="PUT" class="ajax-form" onsubmit="event.preventDefault();">
                        <table class="centered striped datagrid">
                            <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $setting)
                                <tr>
                                    <td>{{ $setting->key }}</td>
                                    <td><input type="number" name="setting_value[{{ $setting->key }}]" value="{{ $setting->value }}" required></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="col s12 right-align action-btn-margin">
                            <button type="submit" class="waves-effect btn">Save</button>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('form').addForm({
        form: '.ajax-form',
        errorMessage: 'Failed',
        successUrl: '/settings'
    });
</script>
@endpush