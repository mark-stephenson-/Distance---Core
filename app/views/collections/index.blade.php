@extends('layouts.master')

@section('header')
    <h1>Collections</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('cms.collections.create'))
            <a href="{{ route('collections.create') }}" class="btn"><i class="icon-plus"></i> New Collection</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Application</th>
                <th>API Key</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $collection)
                <tr>
                    <td>
                        {{ $collection->name }}
                    </td>
                    <td>{{ @$collection->application->name ?: '-' }}</td>
                    <td>
                        {{ $collection->api_key }}
                    </td>
                    <td width="330">
                        @if (Config::get('core.features.hierarchy'))
                            <a href="{{ route('nodes.hierarchy', array($collection->id)) }}" class="btn btn-small"><i class="icon-sitemap"></i> Hierarchy</a>
                        @endif
                        <a href="{{ route("nodes.list", array($collection->id)) }}" class="btn btn-small"><i class="icon-list"></i> Node List</a>

                        @if (Sentry::getUser()->hasAccess('cms.collections.edit'))
                            <a href="{{ route('collections.edit', array($collection->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                        @endif

                        @if (Sentry::getUser()->hasAccess('cms.collections.delete'))
                            <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@stop