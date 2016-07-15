@extends('layouts.master')

@section('header')
    <h1>Reporting - Summary</h1>
@stop

@section('body')
    <div class="container">
        <div class="title-block">
            <h3>PRASE Reporting</h3>
        </div>

        <div class="row">
            <div class="span8">
                @include('reporting.partials.report-table')
            </div>
            <div class="span4">
                <a href="{{ route('reporting.view-pdf', [$fileKey]) }}" class="btn btn-lg btn-primary export-button" data-toggle="tooltip" data-placement="top" title="Will take a long time with a large data set.">Export as PDF</a>
                <a href="{{ route('reporting.view-csv', [$fileKey]) }}" class="btn btn-lg btn-primary export-button">Export as CSV</a>
            </div>
        </div>

        <div class="row">
            @include('reporting.partials.domain-info')
        </div>

        <div class="row">
            <h3>Summary</h3>
            <div id="tabs">
                <ul>
                    <li><a href="#explanation">Report Template Explained</a></li>
                    <li><a href="#summary">Summary Report</a></li>
                    <li><a href="#comments">General Positive Comments Report</a></li>
                    <li><a href="#concerns">General Concerns Report</a></li>
                    <li><a href="#5">Domain Report</a></li>
                    <li><a href="#6">Domain Positive Comments Report</a></li>
                    <li><a href="#7">Domain Concerns Report</a></li>
                </ul>
                @include('reporting.partials.explanation')

                <div id="summary">
                    @include('reporting.partials.summary-key')

                    @include('reporting.partials.domain-summary')

                </div>
                <div id="comments">
                    @include('reporting.partials.positive-comments', ['comments' => $reportData->notes])
                </div>
                <div id="concerns">
                    @include('reporting.partials.concerns', ['concerns' => $reportData->concerns])
                </div>
                <div id="5"></div>
                <div id="6"></div>
                <div id="7"></div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {
            $( "#accordian" ).accordion({
                active: 0,
                collapsible: true
            });

            $( "#tabs" ).tabs({
                active: 0
            });
        });

        $('#preventability').change(
                function(){
                    $(this).closest('form').trigger('submit');
                });

        $('#severity').change(
                function(){
                    $(this).closest('form').trigger('submit');
                });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop