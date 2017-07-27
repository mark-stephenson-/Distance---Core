<?php

    $identifier = uniqid();
    $syntaxes = array();

    foreach (Config::get('core-code-editor') as $code => $d) {
        $syntaxes[$code] = $d['name'];
    }
?>

<input type="hidden" name="columns[{{ $identifier }}][category]" value="{{ $category['name'] }}" />

@if ($data)
    <input type="hidden" name="columns[{{ $identifier }}][name]" value="{{ @$data->name }}" />
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
    {{ Form::label('columns[' . $identifier . '][syntax]', 'Syntax', array('class' => 'control-label')) }}
    <div class="controls">
        {{ Form::select('columns[' . $identifier . '][syntax]', $syntaxes, @$data->syntax, array('class' => 'span4')) }}
    </div>
</div>

<div class="control-group">
    {{ Form::label('columns[' . $identifier . '][description]', 'Description', array('class' => 'control-label')) }}
    <div class="controls">
        {{ Form::text('columns[' . $identifier . '][description]', @$data->description, array('class' => 'span4')) }}
    </div>
</div>
