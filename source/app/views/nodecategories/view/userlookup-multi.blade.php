<?php
    $column_name = $data->{$column->name};
    $items = explode(',', $column_name);

    $userList = array_map(function($userId) {
        return @User::find($userId)->fullName;
    }, $items);

    echo implode(', ', $userList);
?>