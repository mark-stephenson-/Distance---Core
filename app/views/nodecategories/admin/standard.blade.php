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
    {{ Form::label('columns[' . $identifier . '][label]', 'Name', ['class' => 'control-label']) }}
    <div class="controls">
        {{ Form::text('columns[' . $identifier . '][label]', @$data->label, ['class' => 'span4']) }}
    </div>
</div>

<div class="control-group">
    {{ Form::label('columns[' . $identifier . '][description]', 'Description', ['class' => 'control-label']) }}
    <div class="controls">
        {{ Form::text('columns[' . $identifier . '][description]', @$data->description, ['class' => 'span4']) }}
    </div>
</div>

<div class="control-group">
    @if ($category['name'] == 'resource')
        {{-- Catalogue selection with extension filtering --}}
    @elseif ($category['name'] == 'bit')
    @elseif ($category['name'] == 'enum')
    @elseif ($category['name'] == 'enum-multi')
    @elseif ($category['name'] == 'nodelookup')
    @elseif ($category['name'] == 'nodelookup-multi')
    @elseif ($category['name'] == 'date')
    @else
        {{ Form::label('default_value', 'Default Value', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('default_value', null, ['class' => 'span4']) }}
        </div>

        @if ($category['name'] == 'date')
            DATE
        @endif
    @endif
</div>

{{-- Radio buttons (aka booleans) are always going to be present anyway --}}
@if ($category['name'] != 'bit')
    <div class="control-group">
        {{ Form::label("columns[{$identifier}][required]", 'Required', ['class' => 'control-label']) }}
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
