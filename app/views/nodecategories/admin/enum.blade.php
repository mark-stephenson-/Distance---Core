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
    {{ Form::label('columns[' . $identifier . '][values][]', 'Possible Values', ['class' => 'control-label']) }}

        <div class="enum_values">
            @if ($data && $data->values)
                @foreach($data->values as $value)
                <div class="controls spaced">
                    {{ Form::text('columns[' . $identifier . '][values][]', $value, ['class' => 'span4']) }}

                    <button class="btn btn-small js-enum-existing-minus"><i class="icon-trash"></i></button>
                    <button class="btn btn-small"><i class="icon-fixed-width icon-{{ checkCheckbox($value, @$data->default, false, true) }}"></i> Default</button>

                    {{--
                    @if ($category['name'] == 'enum')
                        {{ Form::radio("columns[$identifier][default][]", $value, checkCheckbox($value, @$data->default, false, true), ['class' => 'checkbox']) }}
                    @else
                        {{ Form::checkbox("columns[$identifier][default][]", $value, checkCheckbox($value, @$data->default, false, true), ['class' => 'checkbox']) }}
                    @endif
                    --}}
                </div>
                @endforeach
            @endif

            <div style="display: none" class="js-enum-template">
                <div class="controls spaced">
                    <input type="text" class="span4" id="input_{{ $identifier }}" name="" value="" />

                    <button class="btn btn-small js-enum-minus"><i class="icon-trash"></i></button>
                    <button class="btn btn-small"><i class="icon-fixed-width icon-{{ checkCheckbox($value, @$data->default, false, true) }}"></i> Default</button>
                </div>
            </div>
        </div>


        <div class="controls">
            <p><button class="btn btn-small js-enum-add"><i class="icon-plus"></i> Add Value</button></p>
        </div>
</div>

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