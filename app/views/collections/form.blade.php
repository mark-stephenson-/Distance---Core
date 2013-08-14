@extends('layouts.master')

@section('header')
    @if ($collection->exists)
        <h1>Editing Collection</h1>
    @else
        <h1>New Collection</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($collection, 'collections') }}

    <div class="control-group">
        {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('name', null, ['class' => 'span8']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('api_key', 'API Key', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('api_key', Input::old('api_key', ($collection->api_key) ?: md5(rand())), ['class' => 'span8']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('name', 'Application', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::select('application_id', Application::all()->lists('name', 'id')) }}
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            @if (!$collection->exists)
                {{ Form::submit('Create Collection', ['class' => 'btn']) }}
            @else
                {{ Form::submit('Save Changes', ['class' => 'btn']) }}
            @endif
        </div>
    </div>

    {{ Form::close() }}
@stop