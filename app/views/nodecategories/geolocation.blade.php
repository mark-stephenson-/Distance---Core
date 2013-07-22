<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>
<p><em>{{ @$column->description }}</em></p>
<input type="text" name="nodetype[{{ $column->name }}]" value="{{ Input::old('nodetype.' . $column->name , $value) }}" id="input_{{ $column->name }}" class="validate-geolocation" />