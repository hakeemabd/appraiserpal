<?php
use App\Helpers\OrderViewHelper;

$states = OrderViewHelper::getStateList();
?>
<select name="{{$name}}" id="{{$id}}">
    @foreach($states as $state)
        <option value="{{$state['value']}}"
                {{ !empty($selected) ? 'selected="selected"' : '' }}>
            {{$state['text']}}
        </option>
    @endforeach
</select>