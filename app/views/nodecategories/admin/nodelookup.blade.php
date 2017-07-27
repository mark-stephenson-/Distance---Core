<i class="icon-sort drag_handle"></i>
<?php

    $identifier = uniqid();

?>

<input type="hidden" name="columns[{{ $identifier }}][category]" value="{{ $category['name'] }}" />

@if ($data)
    <input type="hidden" name="columns[{{ $identifier }}][name]" value="{{ $data->name }}" />
@endif

<div class="control-group">
    
    <div class="controls">
        <p><strong>{{ $category['label'] }}</strong> - <a href="#" class="js-remove-category">Remove Field</a></p>
    </div>
</div>

<div class="control-group">
    {{ Form::label('columns[' . $identifier . '][label]', 'Name', array('class' => 'control-label')) }}
    <div class="controls">
        {{ Form::text('columns[' . $identifier . '][label]', @$data->label, array('class' => 'span4 category-name-field')) }}
    </div>
</div>

<div class="control-group">
    {{ Form::label('columns[' . $identifier . '][description]', 'Description', array('class' => 'control-label')) }}
    <div class="controls">
        {{ Form::text('columns[' . $identifier . '][description]', @$data->description, array('class' => 'span4')) }}
    </div>
</div>

<div class="control-group">
    {{ Form::label('columns[' . $identifier . '][lookuptype]', 'Node Type', array('class' => 'control-label')) }}
    <div class="controls">
        {{ Form::select("columns[$identifier][lookuptype]", NodeType::all()->lists('label', 'id'), @$data->lookuptype) }}
    </div>
</div>

<div class="control-group">
    {{ Form::label("columns[{$identifier}][includeWhenExpanded]", 'Include when expanded', array('class' => 'control-label')) }}
    <div class="controls">
        <label class="radio inline">
            {{ Form::radio("columns[{$identifier}][includeWhenExpanded]", 1, popRadio(1, @$data->includeWhenExpanded)) }} Yes
        </label>
        <label class="radio inline">
            {{ Form::radio("columns[{$identifier}][includeWhenExpanded]", 0, popRadio(0, @$data->includeWhenExpanded, true)) }} No
        </label>
    </div>
</div>