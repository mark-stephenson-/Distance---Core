@extends('layouts.master')

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('body')
    
    <div class="dd" id="nestable">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                {{ $branch->node_id }}
            @endforeach
        </ol>
    </div>

@stop