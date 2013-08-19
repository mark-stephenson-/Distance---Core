@extends('layouts.master')

@section('header')
    <h1>Add New Version - App Distribution</h1>
@stop

@section('body')
    {{ formModel($new, 'app-distribution', array('enctype' => 'multipart/form-data')) }}
    
    <div class="control-group">
        {{ Form::label('version', 'Version', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('version', null, array('class' => 'span11')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('platform', 'Platform', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('platform', ['-', 'android' => 'Android', 'ios' => 'iOS']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('environment', 'Environment', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('environment', ['-', 'testing' => 'Testing', 'production' => 'Production']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('release_notes', 'Release Notes', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::textarea('release_notes', null, array('class' => 'span11', 'rows' => 4)) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('file', 'Application File', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::file('file') }}
        </div>
    </div>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Upload New Version" />
        <a href="{{ route('app-distribution.index') }}" class="btn">Back</a>
    </div>

    {{ Form::close() }}
@stop