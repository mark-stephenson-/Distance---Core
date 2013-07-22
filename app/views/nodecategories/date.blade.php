<?php
    if (!isset($data)) {
        if (!@$column->default) {
            $value = 'today';
        } else {
            $value = $column->default;
        }
    } else {
        $value = @$data->{$column->name};
    }
?>
<input type="text" name="nodetype[{{ $column->name }}]" value="{{ Input::old('nodetype.' . $column->name, date('d/m/Y', strtotime($value))) }}" id="input_{{ $column->name }}" class="validate-date datepicker" />
<p><em>{{ @$column->description }}</em></p>