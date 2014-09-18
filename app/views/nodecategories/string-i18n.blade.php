<?php
	$identifier = uniqid();
	
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
    
    $data = array_map('str_getcsv', file('https://developers.google.com/adwords/api/docs/appendix/languagecodes.csv'));
    $head = array_shift($data);
    
    $keys = array();
    $vals = array();
    
    foreach($data as &$row) {
    	array_push($keys, $row[1]);
    	array_push($vals, $row[0]);
    }
    
    $languages = array_combine($keys, $vals);
?>

<script type="text/javascript">
    $(function() {
        $('#{{ $identifier }} select').change(function() {
        	$("#{{ $identifier }} input").attr("type", "hidden");
        	$("#{{ $identifier }} [name='translation[{{ $column->name }}][" + $(this).val() + "]']").attr("type", "text");
        });
        $('#{{ $identifier }} select').change();
    });
</script>

<div id="{{ $identifier }}" class="span8" style="margin-left: 0px">
    {{ Form::hidden('nodetype['.$column->name.']', $value) }}
	@foreach ($keys as $key)
		{{ Form::hidden('translation['.$column->name.']['.$key.']', I18nString::whereKey($value)->whereLang($key)->get()->first() ? I18nString::whereKey($value)->whereLang($key)->get()->first()->value : "", array("class" => "span8")) }}
	@endforeach

	{{ Form::select('language', $languages, 'en', array("class" => "span3")) }}
</div>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif