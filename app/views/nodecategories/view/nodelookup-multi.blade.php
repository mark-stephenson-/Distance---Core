<?php
    $column_name = $data->{$column->name};
    $items = array_filter(explode(', ', $column_name));
?>

@foreach ($items as $item)
    <a href="{{ route('nodes.view', array(Request::segment(2), $item)) }}">{{ @Node::find($item)->title }}</a>
@endforeach