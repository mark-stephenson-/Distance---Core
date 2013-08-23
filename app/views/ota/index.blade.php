@extends('layouts.master')

@section('header')
    <h1>App Distribution</h1>
@stop

@section('body')
    <h2>Current Versions</h2>
    
    {{ Form::open(array('url' => route('app-distribution.update', array(CORE_APP_ID)))) }}
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="25%">iOS Production</th>
                <th width="25%">Android Production</th>
                <th width="25%">iOS Testing</th>
                <th width="25%">Android Testing</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ @Ota::ios()->production()->current()->first()->build_string ?: '-' }}</td>
                <td>{{ @Ota::android()->production()->current()->first()->build_string ?: '-' }}</td>
                <td>{{ @Ota::ios()->testing()->current()->first()->build_string ?: '-' }}</td>
                <td>{{ @Ota::android()->testing()->current()->first()->build_string ?: '-' }}</td>
            </tr>

            <tr>
                <td colspan="4" height="10px"></td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ Form::text('', URL::to('ota'), array('class' => 'span10', 'style' => 'text-align: center', 'disabled')) }}
                </td>
                <td colspan="2">
                    {{ Form::text('', URL::to('ota/testing'), array('class' => 'span10', 'style' => 'text-align: center', 'disabled')) }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>Production Password</p>
                    {{ Form::text('production_password', Setting::getConfig('ota-production_password'), array('class' => 'span10', 'style' => 'text-align: center')) }}
                </td>
                <td colspan="2">
                    <p>Testing Password</p>
                    {{ Form::text('testing_password', Setting::getConfig('ota-testing_password'), array('class' => 'span10', 'style' => 'text-align: center')) }}
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <a href="{{ route('app-distribution.create', array(CORE_APP_ID)) }}" class="btn">Upload New Version</a>
                    {{ Form::submit('Save Passwords', array('class' => 'btn btn-primary')) }}
                </td>
            </tr>
        </tbody>
    </table>
    {{ Form::close() }}

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
                <td>
                    @if ( $versions = Ota::ios()->production()->order()->get() and count($versions) )
                        @foreach ( $versions as $v )
                            {{ $v->build_string }}<br />
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ( $versions = Ota::android()->production()->order()->get() and count($versions) )
                        @foreach ( $versions as $v )
                            {{ $v->build_string }}<br />
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ( $versions = Ota::ios()->testing()->order()->get() and count($versions))
                        @foreach ( $versions as $v )
                            {{ $v->build_string }}<br />
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ( $versions = Ota::android()->testing()->order()->get() and count($versions))
                        @foreach ( $versions as $v )
                            {{ $v->build_string }}<br />
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <style>
        .table th, .table td { text-align: center !important }
    </style>
@stop