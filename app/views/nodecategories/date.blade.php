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

{{ Form::text('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, date('d/m/Y', strtotime($value))), ['class' => 'span8 validate-date datepicker']) }}

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif