<?php
    $column_name = $data->{$column->name};

    if ( $column_name ) {
?>
    <a href="{{ route('nodes.view', array($column_name)) }}">{{ @Node::find($column_name)->title }}</a>
<?php
    } else {
        echo 'N/A';
    }
?>