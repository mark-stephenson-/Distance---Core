@extends('layouts.master')

@section('header')
    <h1>Add New Version - App Distribution</h1>
@stop

@section('body')
    {{ formModel($new, 'app-distribution', array('enctype' => 'multipart/form-data')) }}

    <div class="control-group">
        {{ Form::label('version', 'Version', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('version', Input::old('version'), array('class' => 'span11')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('platform', 'Platform', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('platform', array('-', 'android' => 'Android', 'ios' => 'iOS'), Input::old('platform')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('environment', 'Environment', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('environment', array('-', 'testing' => 'Testing', 'production' => 'Production'), Input::old('environment')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('release_notes', 'Release Notes', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::textarea('release_notes', Input::old('release_notes'), array('class' => 'span11', 'rows' => 4)) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('file', 'Application File', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::file('application') }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('profile', 'Provisioning Profile (iOS)', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::file('profile') }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('certificate', 'Certificate (Windows)', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::file('certificate') }}
        </div>
    </div>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Upload New Version" />
        <a href="{{ route('app-distribution.index', array(CORE_APP_ID)) }}" class="btn">Back</a>
    </div>

    {{ Form::close() }}
@stop
