<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

{{ Form::text('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, $value), ['class' => 'span8 validate-geolocation']) }}

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif