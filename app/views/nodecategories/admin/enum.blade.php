<?php

    $identifier = uniqid();

?>

<input type="hidden" name="columns[{{ $identifier }}][category]" value="{{ $category['name'] }}" />

@if ($data)
    <input type="hidden" name="columns[{{ $identifier }}][name]" value="{{ $data->name }}" />
@endif

<i class="icon-sort drag_handle"></i>

<div class="input">
    <label for="input_{{ $identifier }}">
        Name <em>{{ $category['label'] }}</em>
        <a href="#" class="js-remove-category">Remove</a>
</label>
    <input type="text" id="input_{{ $identifier }}" name="columns[{{ $identifier }}][label]" value="{{ @$data->label }}">
</div>

<div class="input">
    Description
    <label for="input_{{ $identifier }}_description">
        <input type="text" id="input_{{ $identifier }}_description" name="columns[{{ $identifier }}][description]" value="{{ @$data->description }}">
    </label>
</div>

<div class="input">
    <label for="input_{{ $identifier }}">Possible Values</label>
    <p><a href="#" class="js-enum-add">Add Value<i class="icon-plus"></i></a></p>
    <div class="enum_values">
        @if ($data && $data->values)
            @foreach($data->values as $value)
                <div style="margin-bottom: 10px">
                    <input type="text" id="input_{{ $identifier }}" readonly="true" name="columns[{{ $identifier }}][values][]" value="{{ $value }}" />
                    @if ($category['name'] == 'enum')
                        <div class="checkbox">
                            <label class="inline">
                                <p>
                                    Default 
                                    {{ Form::radio("columns[$identifier][default]", $value, popRadio($value, @$data->default)) }}
                                </p>
                            </label>
                        </div>
                    @else
                        <div class="checkbox">
                            <label class="inline">
                                <p>
                                    Default 
                                    {{ Form::checkbox("columns[$identifier][default][]", $value, checkCheckbox($value, @$data->default)) }}
                                </p>
                            </label>
                        </div>
                    @endif
                    <a href="#" class="js-enum-existing-minus">Remove <i class="icon-minus"></i></a>
                </div>
            @endforeach
        @else
            <div style="margin-bottom: 10px">
                <input type="text" id="input_{{ $identifier }}" name="columns[{{ $identifier }}][values][]" value="" />
                <a href="#" class="js-enum-minus">Remove <i class="icon-minus"></i></a>
            </div>
        @endif

        <div style="display: none" class="js-enum-template">
            <div>
                <input type="text" id="input_{{ $identifier }}" name="" value="" />
                <a href="#" class="js-enum-minus">Remove <i class="icon-minus"></i></a>
            </div>
        </div>
    </div>
</div>


<div class="checkbox">
    Required
    <label class="inline">
        {{ Form::radio("columns[{$identifier}][required]", 1, popRadio(1, @$data->required)) }} Yes
        {{ Form::radio("columns[{$identifier}][required]", 0, popRadio(0, @$data->required, true)) }} No
    </label>
</div>

<div class="input">
    Permission
    <label for="input_{{ $identifier }}_perms">
        <?php
            $possiblePermissions = array(
                'core' => 'Core Admin',
                'super' => 'Super Admin',
                'admin' => 'Trust Admin',
                'write' => 'Author'
            );

            if (isset($data)) {
                $selected = @$data->perms;
            } else {
                $selected = 'write';
            }

            if (!$selected) {
                $selected = 'write';
            }
        ?>
        {{ Form::select("columns[$identifier][perms]", $possiblePermissions, $selected) }}
    </label>
</div>