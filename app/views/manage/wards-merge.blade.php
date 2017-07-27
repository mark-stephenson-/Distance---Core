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
    <h1>Merging Ward</h1>
@stop

@section('body')

    {{ Form::open(array('autocomplete' => 'off')) }}

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            <div class="control-group">
                {{ Form::label('name', 'Merge ' . $wardData->name . ' with (select from the drop-down list below)', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::select('chosen_ward', $possibleMerges->lists('ward_name', 'id'), null, array('class' => 'span11')) }}
                    {{ Form::hidden('old_name', $wardData->name, array('class' => 'span11')) }}
                </div>
            </div>

            @if ($ward->exists)
                <div class="control-group">
                    {{ Form::label('change_comment', 'Change Comment', array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('change_comment', Input::old('change_comment'), array('class' => 'span11')) }}
                    </div>
                </div>
                <p>If left blank, will be set to "Merged {{$wardData->name}} into [chosen ward]"</p>
            @endif

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
