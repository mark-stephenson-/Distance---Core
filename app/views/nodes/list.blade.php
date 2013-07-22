@extends('layouts.master')

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Config::get('core.features.hierarchy'))
            <a href="{{ route('nodes.hierarchy', [$collection->id]) }}" class="btn"><i class="icon-sitemap"></i> Hierarchy</a>
        @endif
        @if (Sentry::getUser()->hasAccess('nodes.create'))
            <a href="{{ route('nodes.create', [$collection->id]) }}" class="btn"><i class="icon-plus"></i> New Node</a>
        @endif
    </p>
    
    <table class="table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Node Type</th>
                <th>Status</th>
                <th>Owner</th>
                <th>Created</th>
                <th width="150"></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($nodes as $node)
                @include('nodes.list-row', compact('node', 'nodeTypes'))
            @endforeach
        </tbody>

    </table>

@stop