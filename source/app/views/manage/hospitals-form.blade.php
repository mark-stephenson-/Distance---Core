@extends('layouts.master')

<?php
    if ($hospital->exists) {
        $hospitalData = $hospital->latestRevision();
    } else {
        $hospitalData = new \stdClass;
        $hospitalData->name = null;
    }
?>

@section('header')
    @if ($hospital->exists)
        <h1>Editing Hospital</h1>
    @else
        <h1>New Hospital</h1>
    @endif
@stop

@section('body')
    
    {{ Form::open(array('autocomplete' => 'off')) }}

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            <div class="control-group">
                {{ Form::label('name', 'Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('name', Input::old('name', $hospitalData->name), array('class' => 'span11')) }}
                </div>
            </div>
            

        </div>

    </div>   

    <div class="form-actions">
        @if ($hospital->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Hospital" />
        @endif
    </div>

    {{ Form::close() }}

@stop