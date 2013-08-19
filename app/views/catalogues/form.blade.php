@extends('layouts.master')

@section('header')
    @if ($catalogue->exists)
        <h1>Editing Catalogue</h1>
    @else
        <h1>New Catalogue</h1>
    @endif
@stop

@section('js')

    <script>

        $('.select2-restrictions').select2({
            tags:["{{ implode('", "', Config::get('core.prefrences.default-catalogue-restrictions')) }}"],
            tokenSeparators: [",", " "]
        });

    </script>
    
@stop

@section('body')
    
    {{ formModel($catalogue, 'catalogues') }}

    <div class="control-group">
        {{ Form::label('name', 'Name', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('name', null, array('class' => 'span11')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('restrictions', 'File Restrictions', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::hidden('restrictions', Input::old('restrictions', implode(',', $catalogue->restrictions)), array('class' => 'span11 select2-restrictions', 'data-placeholder' => 'Enter file extensions that are allowed')) }}
            <span class="help-block">Leave this blank to allow any files.</span>
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('collections', 'Collections', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('collections[]', $collections->lists('name', 'id'), $catalogue->collections->lists('id'), array('class' => 'span11 select2', 'multiple' => 'multiple', 'data-placeholder' => 'Select the collections that have access to this catalogue')) }}
        </div>
    </div>

    <div class="form-actions">
        @if ($catalogue->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Catalogue" />
        @endif
    </div>

    {{ Form::close() }}

@stop