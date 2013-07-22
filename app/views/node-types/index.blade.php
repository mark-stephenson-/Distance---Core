@extends('layouts.master')

@section('header')
    <h1>Node Type</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('node-types.create'))
            <a href="{{ route('node-types.create') }}" class="btn"><i class="icon-plus"></i> New Node Type</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Label</th>
                <th>Collections</th>
                <th>Columns</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nodeTypes as $nodeType)
            <tr>
                <td>
                    {{ $nodeType->label }}
                </td>
                <td>
                    @if (count($nodeType->collections))
                        @foreach($nodeType->collections as $collection)
                            {{ $collection->name }}<br />
                        @endforeach
                    @else
                        No Collections
                    @endif
                </td>
                <td>
                    @if (count($nodeType->columns))
                        @foreach($nodeType->columns as $column)
                            {{ $column->label }} <em class="muted">{{ Config::get('node-categories.' . $column->category . '.label') }}</em><br />
                        @endforeach
                    @else
                        No Columns
                    @endif
                </td>
                <td width="330">
                    <a href="{{ route('node-types.edit', array($nodeType->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop