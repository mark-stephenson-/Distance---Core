@extends('layouts.master')

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('body')

    <p class="pull-right">
        <a href="{{ route('nodes.list', [$collection->id]) }}" class="btn"><i class="icon-list"></i> Node List</a>
        @if (Sentry::getUser()->hasAccess('nodes.create'))
            <a href="{{ route('nodes.create', [$collection->id]) }}" class="btn"><i class="icon-plus"></i> New Root Node</a>
        @endif
    </p>
    
    <div class="dd" id="nestable">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                {{ $branch->node_id }}
            @endforeach
        </ol>
    </div>

@stop