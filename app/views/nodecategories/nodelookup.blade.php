<?php

    // We need to do a bit of formatting on the dropdowns so the enum var is returned, not the array key
    if ($data && $col = $data->{$column->name}) {
        $db_object = Node::whereId($col)->first(array('id', 'title'));
    } else {
        $db_object = null;
    }

?>

{{ Form::hidden('nodetype[' . $column->name . ']', null, array('id' => 'input_' . $column->name)) }}
<p><em>{{ @$column->description }}</em></p>

<script>

var {{ str_replace('-', '_', $column->name) }}_preload_data = [];
@if ($db_object)
    {{ str_replace('-', '_', $column->name) }}_preload_data.push({ 'id': {{ $db_object->id }}, 'text': "{{ $db_object->title }}" });
@endif

    $(document).ready(function() {

        $('#input_{{ $column->name }}').select2({

            placeholder: "Start Typing To Search",
            minimumInputLength: 2,
            maximumSelectionSize: 1,
            multiple:true,
            ajax: {
                url: '{{ route('nodes.lookup') }}?type={{ $column->lookuptype }}',
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term
                    }
                },
                results: function (data, page) {
                    return data;
                }
            }
        });

        $('#input_{{ $column->name }}').select2('data', {{ str_replace('-', '_', $column->name) }}_preload_data);

    });
</script>