@extends('layouts.master')

@section('header')
    <h1>Data Export</h1>
@stop

@section('body')
    <p class="lead">Please pick a question set to export the data</p>

    {{ Form::open() }}

    <div class="control-group">
        {{ Form::label('question_set', 'Question Set', array('class' => 'control-label')) }}
        <div class="controls">
            <select name="question_set" class="chosen" style="width: 100%">
				@foreach($questionSets as $set)
					<option value="{{ $set->id }}">{{ $set->status }} - Created: {{ $set->created_at }} / Published: {{ $set->published_at }} / Retired: {{ $set->retired_at }}</option>
				@endforeach
		    </select>
        </div>
    </div>

    {{ Form::submit('Export', ['class' => 'btn']) }}

    {{ Form::close() }}
@stop