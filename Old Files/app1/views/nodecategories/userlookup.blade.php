<?php

    $items = User::forLookup($column->lookuptype);
    foreach ( $items as $_user ) {
        $user_list[$_user->id] = $_user->full_name;
    }

?>

{{ Form::select('nodetype[' . $column->name . ']', $user_list, @$data->{$column->name}, array('id' => 'input_' . $column->name)) }}
<p><em>{{ @$column->description }}</em></p>

<script>

    $(document).ready(function() {

        $('#input_{{ $column->name }}').select2({

            placeholder: "Start Typing To Search",
            maximumSelectionSize: 1,
        });

    });
</script>