<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>
<div class="checkbox">
    <label class="inline">
        {{ Form::radio('nodetype[' . $column->name . ']', 1, popRadio(1, $value)) }} Yes
        {{ Form::radio('nodetype[' . $column->name . ']', 0, popRadio(0, $value, true)) }} No
    </label>
</div>
<p><em>{{ @$column->description }}</em></p>