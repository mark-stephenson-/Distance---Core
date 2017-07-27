<?php
    $date = '';
?>

@if ( strtotime($data->{$column->name}) > 0 )
    {{ date('d/m/Y h:i', strtotime($data->{$column->name})) }}
@else
    N/A
@endif