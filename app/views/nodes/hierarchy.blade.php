@extends('layouts.master')

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('js')
    <script>

        var currentNodeId = 0;

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
                $('#addNodeModalExisting').show();
            } else {
                $('#addNodeModalExisting').hide();
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

        $('#openRootNodeModal').on('click', function(e) {
            e.preventDefault();
            currentNodeId = 0;
            $('#addNodeModal').modal('show');
        });

        $('.open-node-modal').on('click', function(e) {
            e.preventDefault();
            currentNodeId = $(this).attr('data-id');
            $('#addNodeModal').modal('show');
        });

        $('#addNodeConfirm').on('click', function(e) {

            var nodeType = $('#node_type_select').val();

            if (nodeType != 'existing') {
                var url = "{{ route('nodes.create', [$collection->id]) }}/" + nodeType + '/' + currentNodeId;
            } else {
                var nodeId = $('#existing_node_select').val();

                if (nodeId) {
                    var url = "{{ route('nodes.link') }}/{{ $collection->id }}/" + nodeId + '/' + currentNodeId;
                } else {
                    alert('nope');
                }
            }
            window.location = url;

        });
    </script>
@stop

@section('body')

    <div class="btn-group pull-right">
        <a href="{{ route('nodes.list', [$collection->id]) }}" class="btn"><i class="icon-list"></i> Node List</a>
        @if (Sentry::getUser()->hasAccess('nodes.create'))
            <a href="#" class="btn" id="openRootNodeModal"><i class="icon-plus"></i> New Root Node</a>
            <!-- {{ route('nodes.create', [$collection->id]) }} -->
        @endif
    </div>
    
    <div class="dd">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                <li class="dd-item" data-id="{{ $branch->id }}">
                    <div class="pull-right node-hierarchy-buttons">
                        {{ $branch->node->statusBadge }}
                        <div class="btn-group">
                            <a href="{{ route('nodes.view', [$branch->node->id, 'branch', $branch->id]) }}" rel="tooltip" title="View" class="btn btn-mini"><i class="icon-search"></i></a>
                            <a href="{{ route('nodes.edit', [$branch->node->id, 'branch', $branch->id]) }}" rel="tooltip" title="Edit" class="btn btn-mini"><i class="icon-edit"></i></a>
                            <a href="#" rel="tooltip" data-id="{{ $branch->id }}" title="Add Link" class="btn btn-mini open-node-modal"><i class="icon-link"></i></a>
                            <a href="#" rel="tooltip" title="Remove Link" class="btn btn-mini"><i class="icon-unlink"></i></a>
                        </div>
                    </div>
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

    <div class="modal hide fade" id="addNodeModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add Node</h3>
        </div>
        <div class="modal-body">
            <p>Please select a node type to place as a root node in the hierarchy.</p>
            {{ Form::select('node_type', NodeType::forSelect($collection, true), null, array('id' => 'node_type_select', 'class' => 'select2'))}}
            <div id="addNodeModalExisting" style="display: none;">
                {{ Form::hidden('existing_node', null, array('id' => 'existing_node_select')) }}
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="addNodeConfirm">Add Node</a>
        </div>
    </div>

@stop