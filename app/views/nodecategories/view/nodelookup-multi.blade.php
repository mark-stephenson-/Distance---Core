<?php
    $column_name = $data->{$column->name};
    $items = explode(',', $column_name);
?>

@foreach ($items as $item)
    <a href="{{ route('nodes.view', array($item)) }}">{{ @Node::find($item)->title }}</a>
@endforeach