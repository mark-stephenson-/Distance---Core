@extends('layouts.master')

@section('header')
    <h1>App Distribution</h1>
@stop

@section('body')
    <h2>Current Versions</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>iOS Production</th>
                <th>Android Production</th>
                <th>iOS Testing</th>
                <th>Android Testing</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>

            <tr>
                <td colspan="4" height="10px"></td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ Form::text('', URL::to('ota'), ['class' => 'span10', 'style' => 'text-align: center', 'disabled']) }}
                </td>
                <td colspan="2">
                    {{ Form::text('', URL::to('ota/testing'), ['class' => 'span10', 'style' => 'text-align: center', 'disabled']) }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>Production Password</p>
                    {{ Form::text('production-password', null, ['class' => 'span10']) }}
                </td>
                <td colspan="2">
                    <p>Testing Password</p>
                    {{ Form::text('testing-password', null, ['class' => 'span10']) }}
                </td>
            </tr>
        </tbody>
    </table>

    <hr />

    <h2>Previous Versions</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>iOS Production</th>
                <th>Android Production</th>
                <th>iOS Testing</th>
                <th>Android Testing</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <style>
        .table th, .table td { text-align: center !important }
    </style>
@stop