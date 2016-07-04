@extends('layouts.master')

@section('header')
    <h1>Reporting</h1>
@stop

@section('body')
    <div class="title-block">
        <h3>Bespoke Report Parameters</h3>
    </div>

    <div class="reports-form">
        {{ Form::open(['id' => 'report-form']) }}
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
                {{ Form::submit('Generate Report', array('class' => 'submit-button btn')) }}
            </div>
        {{ Form::close() }}
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {
            $("#period_start").datepicker({ dateFormat: 'dd-mm-yy' });
            $("#period_end").datepicker({ dateFormat: 'dd-mm-yy' });

            $('[name=trust]').on('change', function() {
                var trustId = $(this).val();
                var url = "/reporting/_ajax/" + trustId + "/hospitals";

                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'json'
                }).done(function(data) {
                    $('[name=hospital]').html('');
                    var listitems = '';
                    $.each(data, function(key, value){
                        listitems = '<option value=' + key + '>' + value + '</option>' + listitems;
                    });
                    $('[name=hospital]').append(listitems);
                });
            });

            $('[name=hospital]').on('change', function() {
                var hospitalId = $(this).val();
                var url = "/reporting/_ajax/" + hospitalId + "/wards";

                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'json'
                }).done(function(data) {
                    $('[name=ward]').html('');
                    var listitems = '';
                    $.each(data, function(key, value){
                        listitems = '<option value=' + key + '>' + value + '</option>' + listitems;
                    });
                    $('[name=ward]').append(listitems);
                });
            });

            $('#report-form').on('submit', function(e) {
                e.preventDefault();

                var wardId = $('[name=ward]').val();

                var url = "/reporting/_ajax/" + wardId + "/generate";

                $.ajax({
                    method: 'GET',
                    url: url,
                    data: {
                        'startDate': $('[name=period_start]').val(),
                        'endDate': $('[name=period_end]').val()
                    },
                    dataType: 'json'
                }).done(function(data) {
                    alert(data);
                });
            });
        });
    </script>
@stop