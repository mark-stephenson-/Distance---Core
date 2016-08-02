@extends('layouts.master')

@section('header')
    <h1>Reporting - Summary</h1>
    <style>
        .progress {
            margin-bottom: 0;
        }
    </style>
@stop

<?php

function filterConcerns($concerns) {
    $makeComparer = function($criteria) {
        $comparer = function ($first, $second) use ($criteria) {
            foreach ($criteria as $key => $orderType) {
                // normalize sort direction
                $orderType = strtolower($orderType);
                if ($first->$key < $second->$key) {
                    return $orderType === "asc" ? -1 : 1;
                } else if ($first->$key > $second->$key) {
                    return $orderType === "asc" ? 1 : -1;
                }
            }
            // all elements were equal
            return 0;
        };
        return $comparer;
    };

    $concerns = new \Illuminate\Support\Collection(array_values((array) $concerns));

    if (Input::get('filter', 'preventability') == 'preventability') {
        $concerns = $concerns->sort($makeComparer([
                'preventability' => Input::get('preventability'),
                'severity' => Input::get('severity'),
        ]));
    } else {
        $concerns = $concerns->sort($makeComparer([
                'severity' => Input::get('severity'),
                'preventability' => Input::get('preventability'),
        ]));
    }
    return $concerns;
}

?>

@section('body')
        <div class="title-block">
            <h3>PRASE Reporting</h3>
        </div>

        <div class="clearfix">
            <div class="span8">
                @include('reporting.partials.report-table')
            </div>
            <div class="span4">
                @unless(Input::get('domain'))
                    <a href="{{ route('reporting.view-pdf', [$fileKey, 'type' => Input::get('type')]) }}" class="btn btn-lg btn-primary export-button" data-toggle="tooltip" data-placement="top" title="Will take a long time with a large data set.">Export as PDF</a>
                    <a href="{{ route('reporting.view-csv', [$fileKey, 'type' => Input::get('type')]) }}" class="btn btn-lg btn-primary export-button">Export as CSV</a>
                @endunless
            </div>
        </div>

            @include('reporting.partials.domain-info')

            @if (Input::get('domain'))
                <?php
                    $domain = null;

                    foreach($reportData->domains as $id => $domainValue) {
                        if ($id == Input::get('domain')) {
                            $domain = $domainValue;
                        }
                    }

                ?>
                <h3><a href="{{ Request::url() }}">Report Summary</a> > {{ $domain->name }}</h3>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#summary" data-toggle="tab">Domain Report</a></li>
                        <li><a href="#comments" data-toggle="tab">Domain Positive Comments Report</a></li>
                        <li><a href="#concerns" data-toggle="tab">Domain Concerns Report</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="summary">
                            @include('reporting.partials.summary-key')

                            @include('reporting.partials.domain-questions')

                        </div>
                        <div class="tab-pane" id="comments">
                            @include('reporting.partials.positive-comments', ['comments' => (array) $domain->notes])
                        </div>
                        <div class="tab-pane" id="concerns">
                            @include('reporting.partials.concerns-filter')
                            @include('reporting.partials.concerns', ['concerns' => filterConcerns($domain->concerns)])
                        </div>
                    </div>
            @else
                <h3>Report Summary</h3>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#explanation" data-toggle="tab">Report Template Explained</a></li>
                    <li><a href="#summary" data-toggle="tab">Summary Report</a></li>
                    <li><a href="#comments" data-toggle="tab">General Positive Comments Report</a></li>
                    <li><a href="#concerns" data-toggle="tab">General Concerns Report</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="explanation">
                        @include('reporting.partials.explanation')
                    </div>

                    <div class="tab-pane" id="summary">
                        @include('reporting.partials.summary-key')

                        @include('reporting.partials.domain-summary')
                    </div>
                    <div class="tab-pane" id="comments">
                        @include('reporting.partials.positive-comments', ['comments' => (array) $reportData->notes])
                    </div>
                    <div class="tab-pane" id="concerns">
                        @include('reporting.partials.concerns-filter')
                        @include('reporting.partials.concerns', ['concerns' => filterConcerns($reportData->concerns)])
                    </div>
                </div>
            @endif

@stop

@section('js')
    <script>
        var request = {
            queryString: function(item){
                var value = location.search.match(new RegExp("[\?\&]" + item + "=([^\&]*)(\&?)","i"));
                return value ? value[1] : value;
            }
        }

        $(document).ready(function() {
            $( "#accordian" ).accordion({
                active: 0,
                collapsible: true
            });

            $('.filter-form select').change(function(){
                // Build the URL
                var url = "{{ Request::url() }}?";

                url = url + "filter=" + $('[name=filter]').val();
                url = url + "&preventability=" + $('[name=preventability]').val();
                url = url + "&severity=" + $('[name=severity]').val();

                if (request.queryString("domain")) {
                    url = url + "&domain=" + request.queryString("domain");
                }

                url = url + '#concerns';

                window.location = url;
            });

            $('[data-toggle="tooltip"]').tooltip();

            var url = document.location.toString();
            if (url.match('#')) {
                $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            }

            // Change hash for page-reload
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            })


        });
    </script>
@stop