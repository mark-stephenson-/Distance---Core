@extends('layouts.master')

<?php
    if ($trust->exists) {
        $trustData = $trust->latestRevision();
    } else {
        $trustData = new \stdClass;
        $trustData->name = null;
    }
?>

@section('header')
    @if ($trust->exists)
        <h1>Editing Trust</h1>
    @else
        <h1>New Trust</h1>
    @endif
@stop

@section('body')
    
    {{ Form::open(array('autocomplete' => 'off')) }}

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            <div class="control-group">
                {{ Form::label('name', 'Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('name', Input::old('name', $trustData->name), array('class' => 'span11')) }}
                </div>
            </div>
            

        </div>

    </div>   

    <div class="form-actions">
        @if ($trust->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Trust" />
        @endif
    </div>

    {{ Form::close() }}

@stop