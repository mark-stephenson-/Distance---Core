<?php
    $column_name = $data->{$column->name};
?>
<a href="{{ route('nodes.view', array($column_name)) }}">{{ @\Netsells\Ignaz\Models\Node::find($column_name)->title }}</a>