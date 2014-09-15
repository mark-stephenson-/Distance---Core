<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
   // I18nString::where('key',$column_name)->get()
?>

{{ Form::hidden('nodetype[' . $column->name . ']', null, array('id' => 'input_' . $column->name)) }}

<!--{{ Form::text('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, $value), array('class' => 'span8')) }}
-->
@foreach (I18nString::whereKey($value)->get() as $translation)
	{{ Form::text('nodetype['. $column->name . '_' . $translation->lang .']', $translation->value, array('class' => 'span8')) }}
	<em>({{ $translation->lang }})</em>
@endforeach

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif


