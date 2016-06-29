@extends('layouts.master')

@section('header')
    <h1>Reporting</h1>
@stop

@section('body')
    <div class="title-block">
        <h1>PRASE Reporting</h1>
        <h3>Bespoke Report Parameters</h3>
    </div>

    <div class="reports-form">
        {{ Form::open() }}
            <div class="span9">
                <div class="control-group">

                    {{ Form::label('period_start', 'Report Period', array('class' => 'control-label span2')) }}

                    <div class="controls">
                        {{ Form::text('period_start', Input::old('period_start'), array('class' => 'span4', 'id' => 'period_start')) }}
                        {{ Form::text('period_end', Input::old('period_end'), array('class' => 'span4', 'id' => 'period_end')) }}
                    </div>
                </div>
                <div class="control-group">
                    {{ Form::label('trust', 'Location', array('class' => 'control-label span2')) }}
                    <div class="controls">
                        {{ Form::select('trust', $trusts, Input::old('trust'), array('class' => 'span3')) }}
                        {{ Form::select('hospital', array(), Input::old('hospital'), array('class' => 'span3')) }}
                        {{ Form::select('ward', array(), Input::old('ward'), array('class' => 'span3')) }}
                    </div>
                </div>
            </div>
            <div class="span2">
                <h3>12</h3>
                <p>Responses</p>
                {{ Form::submit('Generate Report') }}
            </div>
        {{ Form::close() }}
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {
            $(function () {
                $("#period_start").datepicker();
                $("#period_end").datepicker();
            });
        });
    </script>
@stop