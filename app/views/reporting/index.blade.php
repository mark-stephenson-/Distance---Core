@extends('layouts.master')

@section('header')
    <h1>Reporting</h1>
@stop

@section('body')
    @if(Sentry::getUser()->hasAccess('cms.export-data.generate-reports'))
        @include('reporting.partials.generate-reports')
        <hr>
    @endif

    <div class="clearfix">
        <div class="title-block">
            <h3>Standard Prase Ward Reports</h3>
        </div>

        <div class="span9 filter-container">
            <div class="row">
                {{ Form::label('trust', 'Location', array('class' => 'control-label span2')) }}
                <div class="controls">
                    <div class="input-group span4">
                        {{ Form::select('trust', $trusts, Input::old('trust'), array('class' => 'trust-select span12', 'data-can-update-standard-table' => true)) }}
                    </div>
                    <div class="input-group span4">
                        {{ Form::select('hospital', array(), Input::old('hospital'), array('class' => 'hospital-select span12', 'data-can-update-standard-table' => true, 'style' => 'display: none;')) }}
                    </div>
                </div>
            </div>
            <div class="row wards-row hide">
                {{ Form::label('wards', 'Ward', array('class' => 'control-label span2')) }}
                <div class="controls">
                    <div class="input-group span4">
                        {{ Form::hidden('wards', null, ['id' => 'wards', 'class' => 'wards-select span12', 'data-can-update-standard-table' => true]) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="standard-reports">
            @include('reporting.partials.standard-reports-table')
        </div>

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

            $('.icon-calendar').click(function () {
                var elemId = $(this).data('trigger');
                if(typeof(elemId) != 'undefined') {
                    $('#' + elemId).focus();
                }
            });


            $('.trust-select').on('change', function() {
                var trustId = $(this).val();
                var $filterContainer = $(this).closest('.filter-container');
                var $hospitalSelect = $filterContainer.find('.hospital-select');
                var $wardsSelect = $filterContainer.find('.wards-select');

                $hospitalSelect.show();
                $wardsSelect.select2("destroy");

                updateHospitalSelect(trustId, $hospitalSelect)
                if($(this).data('can-update-standard-table')) {
                    updateStandardReportsTable({trust_id: trustId});
                }
            });

            $('.hospital-select').on('change', function() {
                var hospitalId = $(this).val();
                var $filterContainer = $(this).closest('.filter-container');
                var $wardsRow = $filterContainer.find('.wards-row');
                var $wardsSelect = $filterContainer.find('.wards-select');

                $wardsRow.show();
                $wardsSelect.select2("destroy");

                updateWardsSelect(hospitalId, $wardsSelect);

                if($(this).data('can-update-standard-table')) {
                    updateStandardReportsTable({hospital_id: hospitalId});
                }
            });

            $('#report-form').on('submit', function(e) {
                e.preventDefault();

                generate($(this));
            });
        });

        $('#pmos_submit').on('click', function() {
            var pmosId = $('#pmos_id').val();
            $('#report-form').append($('<input>', {type: 'hidden', name: 'pmos_id', value: pmosId}));
            $('#report-form').trigger('submit');
        });

        function updateHospitalSelect(trustId, $hospitalSelect) {
            var url = "/reporting/_ajax/" + trustId + "/hospitals";
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'json'
            }).done(function(data) {
                var listitems = '';
                $.each(data, function(key, value){
                    listitems = '<option value=' + key + '>' + value + '</option>' + listitems;
                });
                $hospitalSelect.html(listitems);
            });
        }

        function updateWardsSelect(hospitalId, $wardsSelect)
        {
            $wardsSelect.select2({
                multiple: false,
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
                if($wardsSelect.data('can-update-standard-table')) {
                    updateStandardReportsTable({ward_id: $(this).val()});
                }
            });
        }

        function updateStandardReportsTable(filter)
        {
            var url = '/reporting/_ajax/update_standard_reports_table?' + $.param(filter);
            var $tableContainer = $('.standard-reports');

            $.get(url, function(response) {
                $tableContainer.html(response.markup);
            }, 'json');
        }

        function generate($form)
        {
            var wardId = $form.find('[name=wards]').val();
            var pmosId = $form.find('[name=pmos_id]').val();

            var url = "/reporting/_ajax/" + wardId + "/generate";

            $.ajax({
                method: 'GET',
                url: url,
                data: {
                    'startDate': $form.find('[name=period_start]').val(),
                    'endDate': $form.find('[name=period_end]').val(),
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
                    Prase.alert('danger', xhr.responseText, '.body');
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