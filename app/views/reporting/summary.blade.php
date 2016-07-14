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
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="table-key"><strong>Submissions</strong></td>
                            <td>{{ $reportData->submissions->total }} ({{ $reportData->submissions->male }} Male, {{ $reportData->submissions->female }} Female)</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Response Date Range</strong></td>
                            <td>{{ $start }} - {{ $end }}</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Trust</strong></td>
                            <td>{{ $reportData->trust }}</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Hospital</strong></td>
                            <td>{{ $reportData->hospital  }}</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Ward</strong></td>
                            <td>{{ $reportData->ward }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="span4">
                <a href="#" class="btn btn-lg btn-primary export-button">Export as PDF</a>
                <a href="#" class="btn btn-lg btn-primary export-button">Export as CSV</a>
            </div>
        </div>

        <div class="row">
            <div id="accordian">
                <h3>Patient Safety Domains</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="table-key"><strong>Communication and Team working</strong></td>
                            <td>The effective exchange and sharing of information between staff, patients and departments including written and verbal communication systems. Team working of professionals within a group.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Organisation and Care planning</strong></td>
                            <td>Factors related to the care plan, and the availability of resources for the care plan.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Access to resources</strong></td>
                            <td>The availability of experienced staff, equipment and external resources.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Ward type and layout</strong></td>
                            <td>The patients' experience of the ward environment.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Staff roles and responsibilities</strong></td>
                            <td>Clear supervision and lines of accountability for staff.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Staff training</strong>/td>
                            <td>Staff competency and ability to perform role at appropriate grade.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Delays</strong></td>
                            <td>Delays relating to a specific procedure, or to general aspects of care.</td>
                        </tr>
                        <tr>
                            <td class="table-key"><strong>Dignity and Respect</strong></td>
                            <td>Lorem ipsum</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <h3>Summary</h3>
            <div id="tabs">
                <ul>
                    <li><a href="#explanation">Report Template Explained</a></li>
                    <li><a href="#summary">Summary Report</a></li>
                    <li><a href="#3">General Positive Comments Report</a></li>
                    <li><a href="#4">General Concerns Report</a></li>
                    <li><a href="#5">Domain Report</a></li>
                    <li><a href="#6">Domain Positive Comments Report</a></li>
                    <li><a href="#7">Domain Concerns Report</a></li>
                </ul>
                <div id="explanation">
                    <p>This report will give you a detailed overview of the patient feedback collected on your ward. A chart is presented that will provide you an overview of the patient measures of safety scores against the patient safety domains. Further sections will allow you to look at the patient safety domains and individual questions to asceertain exactly where patient safety concerns or positive comments are being reported.</p>

                    <p>All graphs are displayed using a traffic light system - dark red indicates the most negative response possible, dark green indicates the most positive response possible. It is strongly encouraged that staff view traffic light charts and textual tables together.</p>

                    <p>The report breaks down patients responses into the 8 patient safety domains, patient measures of safety scores and also provides a description of patient reported experiences.</p>
                </div>
                <div id="summary">
                    <div class="key-outer">
                        <div class="key-container span1">
                            <div class="key">Key</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour key-danger"></div>
                            <div class="key">Negative-</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour key-warning"></div>
                            <div class="key">Negative</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour key-neutral"></div>
                            <div class="key">Neutral</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour key-positive"></div>
                            <div class="key">Positive</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour key-success"></div>
                            <div class="key">Positive+</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour"></div>
                            <div class="key">Something Good</div>
                        </div>
                        <div class="key-container span1">
                            <div class="colour"></div>
                            <div class="key">Concern</div>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <th style="width: 20%;">Domain</th>
                            <th style="width: 60%;"></th>
                            <th style="width: 20%;">Notes</th>
                        </thead>
                        <tbody>
                            @foreach($reportData->domains as $domain)
                                <tr>
                                    <td>{{ $domain->name }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="bar bar-danger" style="width: {{ $domain->summary->{"1"} }}%;"></div>
                                            <div class="bar bar-warning" style="width: {{ $domain->summary->{"2"} }}%;"></div>
                                            <div class="bar bar-neutral" style="width: {{ $domain->summary->{"3"} }}%;"></div>
                                            <div class="bar bar-positive" style="width: {{ $domain->summary->{"4"} }}%;"></div>
                                            <div class="bar bar-success" style="width: {{ $domain->summary->{"5"} }}%;"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $somethingGood = false;
                                            $concerns = false;

                                            foreach($domain->questions as $question) {
                                                if(isset($question->notes)) {
                                                    $somethingGood = true;
                                                }

                                                if(isset($question->concerns)) {
                                                    $concerns = true;
                                                }
                                            }
                                        ?>
                                        @if($somethingGood == true)
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        @endif

                                        @if($concerns == true)
                                            <i class="fa fa-exclamation" aria-hidden="true"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="3"></div>
                <div id="4"></div>
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
    </script>
@stop