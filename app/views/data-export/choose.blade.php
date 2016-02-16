@extends('layouts.master')

@section('header')
    <h1>Data Export</h1>
@stop

@section('body')
    <p class="lead">Please pick a question set to export the data</p>

    {{ Form::open(['id' => 'export_form']) }}

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

    {{ Form::submit('Export', ['class' => 'btn', 'id' => 'export_submit']) }}

    <div id="loading" style="float: right; font-size: 18px; display: none;">
        <i class="icon icon-cog icon-spin"></i> Loading, please wait (this may take a few minutes).
    </div>

    {{ Form::close() }}

    <script type="text/javascript">
        $(document).ready(function() {
            $('#export_form').on('submit', function() {
               $('#export_submit').attr('disabled', 'disabled');
               $('#loading').fadeIn();
            });
        });
    </script>
@stop