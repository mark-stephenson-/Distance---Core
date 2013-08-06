<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

{{ Form::textarea('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, $value), ['class' => 'span10']) }}

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif