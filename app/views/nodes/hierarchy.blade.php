@extends('layouts.master')

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('js')
    if ($('.dd').length) {
        $('.dd').nestable({ 
            maxDepth: 50,
            expandBtnHTML: '<button data-action="expand" class="dd-drag-collapse"><i class="icon icon-plus-sign"></i></button>',
            collapseBtnHTML: '<button data-action="collapse" class="dd-drag-collapse"><i class="icon icon-minus-sign"></i></button>'
        });

        $('.dd').on('change', function() {
            json = encodeURIComponent(JSON.stringify($('.dd').nestable('serialize')));

            $.ajax({
                type: 'POST',
                url: "{{ action('NodesController@updateOrder', array($collection->id)) }}",
                data: {order: json}
            });
        });
    }
@stop

@section('body')

    <p class="pull-right">
        <a href="{{ route('nodes.list', [$collection->id]) }}" class="btn"><i class="icon-list"></i> Node List</a>
        @if (Sentry::getUser()->hasAccess('nodes.create'))
            <a href="{{ route('nodes.create', [$collection->id]) }}" class="btn"><i class="icon-plus"></i> New Root Node</a>
        @endif
    </p>
    
    <div class="dd">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                <li class="dd-item">
                    <div class="dd-handle" data-item="{{ $branch->id }}">
                        {{ $branch->node->title }}
                    </div>
                </li>
            @endforeach
        </ol>
    </div>

@stop