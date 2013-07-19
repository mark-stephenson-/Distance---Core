<?php

function formModel($model, $routeName) {
    if (is_subclass_of($model, 'Eloquent')) {

        $class = 'form-horizontal';

        if ($model->exists) {
            return Form::model($model, array('route' => array($routeName . '.update', $model->id), 'method' => 'PUT', 'class' => $class));
        } else {
            return Form::model($model, array('route' => array($routeName . '.store'), 'class' => $class));
        }
    }
}