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
        {{ Form::text('columns[' . $identifier . '][label]', @$data->label, array('class' => 'span4')) }}
    </div>
</div>

<div class="control-group">
    {{ Form::label('columns[' . $identifier . '][description]', 'Description', array('class' => 'control-label')) }}
    <div class="controls">
        {{ Form::text('columns[' . $identifier . '][description]', @$data->description, array('class' => 'span4')) }}
    </div>
</div>

<div class="control-group">
    @if ($category['name'] == 'resource' or $category['name'] == 'html')
        {{-- Catalogue selection with extension filtering --}}
        {{ Form::label('columns[' . $identifier . '][catalogue]', 'Catalogue', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('columns[' . $identifier . '][catalogue]', Catalogue::all()->lists('name', 'id'), @$data->catalogue, array('class' => 'span4')) }}
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
    
    @elseif ($category['name'] == 'bit')
    @elseif ($category['name'] == 'enum')
    @elseif ($category['name'] == 'enum-multi')
    @elseif ($category['name'] == 'nodelookup')
    @elseif ($category['name'] == 'nodelookup-multi')
    @elseif ($category['name'] == 'date')
    @else
        {{ Form::label('default_value', 'Default Value', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('columns[' . $identifier . '][default]', @$data->default, array('class' => 'span4')) }}
        </div>

        @if ($category['name'] == 'date')
            DATE
        @endif
    @endif
</div>

{{-- Radio buttons (aka booleans) are always going to be present anyway --}}
@if ($category['name'] != 'bit')
    <div class="control-group">
        {{ Form::label("columns[{$identifier}][required]", 'Required', array('class' => 'control-label')) }}
        <div class="controls">
            <label class="radio inline">
                {{ Form::radio("columns[{$identifier}][required]", 1, popRadio(1, @$data->required)) }} Yes
            </label>
            <label class="radio inline">
                {{ Form::radio("columns[{$identifier}][required]", 0, popRadio(0, @$data->required, true)) }} No
            </label>
        </div>
    </div>
@endif
