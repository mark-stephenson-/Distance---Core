@extends('layouts.master')

@section('header')
    <h1>Apps</h1>
@stop

@section('body')

    <p class="pull-right">
        <a href="{{ route('apps.create') }}" class="btn"><i class="icon-plus"></i> New App</a>
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>API Key</th>
                <th>Collections</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apps as $app)
            <tr>
                <td>
                    {{ $app->name }}
                </td>
                <td>
                    {{ $app->api_key }}
                </td>
                <td>
                    {{ implode('<br />', $app->collections->lists('name')) }}
                </td>
                <td width="150">
                    <a href="{{ route('apps.edit', array($app->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop