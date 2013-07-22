<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>
<p>
    <em>{{ @$column->description }}</em>

    <!-- <a href="#image_list" data-resource="" class="image_fancybox button">Select Resource</a> -->
</p>
<textarea class="ckeditor" name="nodetype[{{ $column->name }}]" id="input_{{ $column->name }}">{{ Input::old('nodetype.' . $column->name, $value) }}</textarea>

<a href="#resource_list" style="display: none" data-dest="html" id="input_{{ $column->name }}_resource_link" data-resource="input_{{ $column->name }}" class="resource_fancybox">Choose One</a>