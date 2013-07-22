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
    <label for="input_{{ $identifier }}_lookuptype">
        Node Type
    </label>
    {{ Form::select("columns[$identifier][lookuptype]", forSelect('node_types', 'label'), @$data->lookuptype, array('id' => "input_{$identifier}_lookuptype")) }}
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
                $selected = false;
            }

            if (!$selected) {
                $selected = 'write';
            }
        ?>
        {{ Form::select("columns[$identifier][perms]", $possiblePermissions, $selected) }}
    </label>
</div>