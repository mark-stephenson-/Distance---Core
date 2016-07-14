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
                    <thead>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Submissions</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Response Date Range</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Trust</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Hospital</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Ward</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="span4">
                <a href="#" class="btn btn-lg btn-primary">Export Report</a>
            </div>
        </div>

        <div class="row">
            <div id="accordian">
                <h3>Patient Safety Domains</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Communication and Team working</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Organisation and Care planning</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Access to resources</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Ward type and layout</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Staff roles and responsibilities</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Staff training</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Delays</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Dignity and Respect</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <h3>Summary</h3>
            <div id="tabs">
                <ul>
                    <li><a href="#1">Report Template Explained</a></li>
                    <li><a href="#2">Summary Report</a></li>
                    <li><a href="#3">General Positive Comments Report</a></li>
                    <li><a href="#4">General Concerns Report</a></li>
                    <li><a href="#5">Domain Report</a></li>
                    <li><a href="#6">Domain Positive Comments Report</a></li>
                    <li><a href="#7">Domain Concerns Report</a></li>
                </ul>
                <div id="1"></div>
                <div id="2"></div>
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