@extends('layouts.master')

<?php
    if ($ward->exists) {
        $wardData = $ward->latestRevision();
    } else {
        $wardData = new \stdClass;
        $wardData->name = null;
    }
?>

@section('header')
    @if ($ward->exists)
        <h1>Editing Ward</h1>
    @else
        <h1>New Ward</h1>
    @endif
@stop

@section('body')
    
    {{ Form::open(array('autocomplete' => 'off')) }}

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            <div class="control-group">
                {{ Form::label('name', 'Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('name', Input::old('name', $wardData->name), array('class' => 'span11')) }}
                </div>
            </div>
            

        </div>

    </div>   

    <div class="form-actions">
        @if ($ward->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Ward" />
        @endif
    </div>

    {{ Form::close() }}

@stop