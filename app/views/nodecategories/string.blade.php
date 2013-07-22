<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>
<input type="text" name="nodetype[{{ $column->name }}]" value="{{ Input::old('nodetype.' . $column->name, $value) }}" id="input_{{ $column->name }}" style="width: 90%" />
<p><em>{{ @$column->description }}</em></p>