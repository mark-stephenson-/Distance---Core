<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

<label class="radio inline">
    {{ Form::radio('nodetype[' . $column->name . ']', 1, popRadio(1, $value)) }} Yes
</label>
<label class="radio inline">
    {{ Form::radio('nodetype[' . $column->name . ']', 0, popRadio(0, $value, true)) }} No
</label>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif