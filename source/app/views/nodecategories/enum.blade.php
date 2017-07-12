<?php

    // We need to do a bit of formatting on the dropdowns so the enum var is returned, not the array key
    $enum_values = array();
    foreach ($column->values as $key => $value) {
        $enum_values[$value] = $value;
    }

    if (isset($data)) {
        $selected = ($data->{$column->name}) ?: array();
    } else {
        $selected = (@$column->default) ?: array();
    }

?>

{{ Form::select('nodetype[' . $column->name . ']', $enum_values, $selected, array('class' => 'chosen', 'id' => 'input_' . $column->name)) }}
<p><em>{{ @$column->description }}</em></p>