@extends('layouts.master')

@section('header')
    <h1>Reporting</h1>
@stop

@section('body')
    <div class="title-block">
        <h3>Bespoke Report Parameters</h3>
        <p>You must select a date range and at least one ward to generate a report.</p>
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
                        {{ Form::select('hospital', array(), Input::old('hospital'), array('class' => 'span3', 'style' => 'display: none;')) }}
                        {{ Form::hidden('wards', null, ['id' => 'wards']) }}
                    </div>
                </div>
            </div>
            <div class="span2">
                {{ Form::submit('Generate Report', array('class' => 'submit-button btn', 'id' => 'generate', 'disabled')) }}
            </div>
        {{ Form::close() }}
    </div>

    <div class="modal hide fade" id="pmos_modal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Please choose a question set.</h3>
        </div>
        <div class="modal-body">
            <select name="pmos_id" id="pmos_id"></select>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="pmos_submit">Save changes</a>
        </div>
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {

            if ($('[name=trust]').val()) {
                window.location.reload(true);
            }

            $("#period_start").datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true }).on('change', function() {
                checkGenerate();
            });

            $("#period_end").datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true  }).on('change', function() {
                checkGenerate();
            });

            $('[name=trust]').on('change', function() {
                var trustId = $(this).val();
                var url = "/reporting/_ajax/" + trustId + "/hospitals";

                $('[name=hospital]').show();
                $("#wards").select2("destroy");

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

                $("#wards").select2("destroy");

                $('#wards').select2({
                    multiple: true,
                    ajax: {
                        url: '/reporting/_ajax/'+hospitalId+'/wards',
                        dataType: 'json',
                        data: function(term, page) {
                            return {
                                q: term
                            }
                        },
                        results: function(data, page) {
                            return data;
                        }
                    }
                }).on('change', function(e) {
                    checkGenerate();
                });
            });

            $('#report-form').on('submit', function(e) {
                e.preventDefault();

                generate();
            });
        });

        $('#pmos_submit').on('click', function() {
            var pmosId = $('#pmos_id').val();
            generate(pmosId);
        });

        function generate(pmosId) {
            if (typeof pmosId == undefined) {
                pmosId = null;
            }
            var wardId = $('[name=wards]').val();

            var url = "/reporting/_ajax/" + wardId + "/generate";

            $.ajax({
                method: 'GET',
                url: url,
                data: {
                    'startDate': $('[name=period_start]').val(),
                    'endDate': $('[name=period_end]').val(),
                    'pmosId': pmosId
                },
                dataType: 'json'
            }).done(function(data) {
                window.location = "/reporting/view/" + data;
            }).fail(function(xhr) {
                if (xhr.status == 416) {

                    $('#pmos_id').html('');
                    var listitems = '';
                    $.each(JSON.parse(xhr.responseText), function(key, value){
                        listitems = '<option value=' + key + '>' + value + '</option>' + listitems;
                    });
                    $('#pmos_id').append(listitems);

                    $('#pmos_modal').modal({

                    });
                }

                if (xhr.status == 404) {
                    alert(xhr.responseText);
                }
            });
        }

        function checkGenerate()
        {
            canGenerate = true;

            if (!$("#period_start").val().length) {
                canGenerate = false;
            }

            if (!$("#period_end").val().length) {
                canGenerate = false;
            }

            if (!$("#wards").val().length) {
                canGenerate = false;
            }

            if (!canGenerate) {
                $('#generate').attr('disabled', 'disabled');
            } else {
                $('#generate').removeAttr('disabled');
            }
        }
    </script>
@stop