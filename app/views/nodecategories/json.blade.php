<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>
<p><em>{{ @$column->description }}</em></p>
<textarea name="nodetype[{{ $column->name }}]" id="input_{{ $column->name }}" class="validate-json">{{ Input::old('nodetype.' . $column->name, $value) }}</textarea>