<?php

    // We need to do a bit of formatting on the dropdowns so the enum var is returned, not the array key
    if ($data && $col = $data->{$column->name}) {
        $items = explode(',', $col);
        if (count($items)) {
            $db_objects = Node::whereIn('id', $items)->get(array('id', 'title'));
        } else {
            $db_objects = array();
        }
    } else {
        $db_objects = array();
    }

    // print_r($db_objects);

?>

{{ Form::hidden('nodetype[' . $column->name . ']', null, array('id' => 'input_' . $column->name)) }}
<p><em>{{ @$column->description }}</em></p>

<script>

var {{ str_replace('-', '_', $column->name) }}_preload_data = [];
@foreach($db_objects as $obj)
    {{ str_replace('-', '_', $column->name) }}_preload_data.push({ 'id': {{ $obj->id }}, 'text': "{{ $obj->title }}" });
@endforeach

    $(document).ready(function() {

        $('#input_{{ $column->name }}').select2({

            placeholder: "Start Typing To Search",
            minimumInputLength: 2,
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