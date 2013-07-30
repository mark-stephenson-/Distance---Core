<?php

function replaceNavigationParams($params) {
    foreach($params as &$param) {
        if ($param == '[collection-id]') {
            $param = ($collection = Collection::current()) ? $collection->id : 0;
        }
    }

    return $params;
}

function formModel($model, $routeName, $atts = array()) {

    if ($model->exists) {
        $customAttributes = array('route' => array($routeName . '.update', $model->id), 'method' => 'PUT');
    } else {
        $customAttributes = array('route' => array($routeName . '.store'));
    }

    $atts['class'] = 'form-horizontal';

    $atts = array_merge($customAttributes, $atts);

    return Form::model($model, $atts);
}

function findObjectInArray($array = array(), $item = '', $type = '') {

    foreach($array as $object) {
        if ($object->$type == $item) {
            return $object;
        }
    }

    return null;
}

function checkCheckbox($value, $data = array(), $checkedByDefault = false, $icon = false) {

    $data = ($data) ?: array();
    $data = (!is_array($data)) ? array($data) : $data;
    $return = in_array($value, $data);

    if (!$icon) return $return;

    return (($return) ? 'ok' : 'remove');
}

function popRadio( $value, $data, $checkedByDefault = false) {
    if (!$data) return $checkedByDefault;
    return ($value == $data);
}

function columnExists($column_name, $table_name) {
    return (DB::table('information_schema.columns')
                ->whereTableSchema( Config::get('database.connections.mysql.database') )
                ->whereTableName($table_name)
                ->whereColumnName($column_name)
                ->count() > 0);
}

function tableExists($table_name) {
    return (DB::table('information_schema.tables')
                ->whereTableSchema( Config::get('database.connections.mysql.database') )
                ->whereTableName($table_name)
                ->count() > 0);
}