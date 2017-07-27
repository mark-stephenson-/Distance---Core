@extends('layouts.master')

<?php
    if ($ward->exists) {
        $wardData = $ward->latestRevision();
    } else {
        $wardData = new \stdClass();
        $wardData->name = null;
    }
?>

@section('header')
    <h1>Deleting Ward</h1>
@stop

@section('body')

    {{ Form::open(array('autocomplete' => 'off')) }}

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            @if ($ward->exists)
                <div class="control-group">
                    {{ Form::label('change_comment', 'Delete Comment', array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('change_comment', Input::old('change_comment'), array('class' => 'span11')) }}
                    </div>
                </div>
                <p>If left blank, will be set to "Deleted {{$wardData->name}}"</p>
            @endif

        </div>

    </div>

    <div class="form-actions">
        @if ($ward->exists)
            <input type="submit" class="btn btn-primary" value="Delete Ward" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Ward" />
        @endif
    </div>

    {{ Form::close() }}

@stop
