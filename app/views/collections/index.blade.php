@extends('layouts.master')

@section('header')
    <h1>Collections</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('collections.create'))
            <a href="{{ route('collections.create') }}" class="btn"><i class="icon-plus"></i> New Collection</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
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
                <td>
                    {{ $collection->api_key }}
                </td>
                <td width="330">
                    <a href="#" class="btn btn-small"><i class="icon-sitemap"></i> Hierarchy</a>
                    <a href="#" class="btn btn-small"><i class="icon-list"></i> Node List</a>
                    <a href="#" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop