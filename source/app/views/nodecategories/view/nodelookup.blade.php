<?php
    $column_name = $data->{$column->name};
?>

@if ( $column_name )
    <a href="{{ route('nodes.view', array(CORE_APP_ID, CORE_COLLECTION_ID, $column_name)) }}">{{ @Node::find($column_name)->title }}</a>
@else
    N/A
@endif