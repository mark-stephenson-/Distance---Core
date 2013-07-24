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

    $('#node_type_select').on('change', function(e) {
        if ($(this).val() == 'existing') {
            $('#addRootNodeModalExisting').show();
        } else {
            $('#addRootNodeModalExisting').hide();
        }
    });

    $('#existing_node_select').select2({

        placeholder: "Start Typing To Search",
        minimumInputLength: 2,
        maximumSelectionSize: 1,
        multiple:false,
        ajax: {
            url: '{{ route('nodes.lookup') }}',
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term,
                    c: {{ $collection->id }}
                }
            },
            results: function (data, page) {
                return data;
            }
        }
    });
@stop

@section('body')

    <div class="btn-group pull-right">
        <a href="{{ route('nodes.list', [$collection->id]) }}" class="btn"><i class="icon-list"></i> Node List</a>
        @if (Sentry::getUser()->hasAccess('nodes.create'))
            <a href="#addRootNodeModal" role="button" class="btn" data-toggle="modal"><i class="icon-plus"></i> New Root Node</a>
            <!-- {{ route('nodes.create', [$collection->id]) }} -->
        @endif
    </div>
    
    <div class="dd">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                <li class="dd-item" data-id="{{ $branch->id }}">
                    <div class="dd-handle">
                        {{ $branch->node->title }}
                    </div>

                    @if (count($branch->getChildren()))
                        @include('nodes.branch', array('branches' => $branch->getChildren()))
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    <div class="modal hide fade" id="addRootNodeModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add Node</h3>
        </div>
        <div class="modal-body">
            <p>Please select a node type to place as a root node in the hierarchy.</p>
            {{ Form::select('node_type', NodeType::forSelect($collection, true), null, array('id' => 'node_type_select', 'class' => 'select2'))}}
            <div id="addRootNodeModalExisting" style="display: none;">
                {{ Form::hidden('existing_node', null, array('id' => 'existing_node_select')) }}
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary">Add Node</a>
        </div>
    </div>

@stop