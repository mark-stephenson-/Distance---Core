<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

{{ Form::textarea('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, $value), array('class' => 'span8 validate-json')) }}

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif