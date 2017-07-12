<?php
    $column_name = $data->{$column->name};
?>

@if ( $column_name )
    {{ @User::find($column_name)->fullName }}
@else
    N/A
@endif