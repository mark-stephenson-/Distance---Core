@extends('layouts.master')

@section('header')
    @if ($node->exists)
        <h1>Editing Node</h1>
    @else
        <h1>New Node</h1>
    @endif
@stop

@section('js')

    $('#js-owner-select').select2();

@stop

@section('body')
    
    @if ($node->exists)
        {{ Form::open(['route' => ['nodes.update', $node->id, $revisionData->id, $branchId], 'class' => 'form-horizontal']) }}
    @else
        {{ Form::open(['route' => ['nodes.store', $collection->id, $nodeType->id, $parentId], 'class' => 'form-horizontal']) }}
    @endif
    
    <div class="control-group">
        {{ Form::label('title', 'Title', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('title', null, ['class' => 'span8']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('owned_by', 'Owner', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::select('owned_by', $node->potentialOwners(), null, ['class' => 'span8', 'id' => 'js-owner-select']) }}
        </div>
    </div>

    <div class="well">
        @foreach($nodeType->columns as $column)
            <div class="control-group">
                {{ Form::label($column->name, $column->label, ['class' => 'control-label']) }}
                <div class="controls">
                    @include('nodecategories.' . $column->category, array('column' => $column, 'node' => $node, 'data' => @$revisionData))
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-actions">
        @if ($node->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Node" />
        @endif
    </div>

@stop