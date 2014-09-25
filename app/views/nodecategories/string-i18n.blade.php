<?php
	$identifier = uniqid();
	$language = 'en';

    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
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
    
	@foreach (Config::get("languages.list") as $key => $val)
		<?php
			if (isset($data)) {
				$i18nString = I18nString::whereKey($value)->whereLang($key)->get()->first();
				$translation = $i18nString ? $i18nString->value : "";
			} else { $translation = ""; }
		?>
		{{ Form::hidden('translation['.$column->name.']['.$key.']', $translation, array("class" => "span8")) }}
	@endforeach

	{{ Form::select("language", Config::get("languages.list"), $language) }}
</div>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif