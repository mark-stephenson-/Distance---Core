@extends('layouts.master')

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('js')
    <script>

        var currentNodeId = 0;

        if ($('.dd').length) {
            @if (Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.hierarchy-management'))
                $('.dd').nestable({ 
                    maxDepth: 50,
                    expandBtnHTML: '<button data-action="expand" class="dd-drag-collapse"><i class="icon icon-plus-sign"></i></button>',
                    collapseBtnHTML: '<button data-action="collapse" class="dd-drag-collapse"><i class="icon icon-minus-sign"></i></button>'
                });
            @endif

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
            currentNodeId = $(this).closest('.dd-item').attr('data-id');
            $('#addNodeModal').modal('show');
        });

        $('.open-remove-link-modal').on('click', function(e) {
            e.preventDefault();
            currentNodeId = $(this).closest('.dd-item').attr('data-id');
            $('#deleteLinkModal').modal('show');
        });

        $('#deleteLinkConfirm').on('click', function(e) {

            e.preventDefault();

            var url = "{{ route('nodes.unlink') }}/{{ $collection->id }}/" + currentNodeId;
            window.location = url;

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

        @if ( count(NodeType::forSelect($collection, false, 'create')) )
            <a href="#" class="btn" id="openRootNodeModal"><i class="icon-plus"></i> New Root Node</a>
        @endif
    </div>
    
    <div class="dd">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                <li class="dd-item" data-id="{{ $branch->id }}">
                    <div class="pull-right node-hierarchy-buttons">
                        <small class="muted"><em>{{ $nodeTypes[$branch->node->node_type]->label }}</em></small> {{ $branch->node->statusBadge }}
                        <div class="btn-group">

                            @if ( Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.' . $branch->node->nodetype->name . '.read'))
                                <a href="{{ route('nodes.view', [$collection->id, $branch->node->id, 'branch', $branch->id]) }}" rel="tooltip" title="View" class="btn btn-mini"><i class="icon-search"></i></a>
                            @endif

                            @if ( Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.' . $branch->node->nodetype->name . '.update'))
                                <a href="{{ route('nodes.edit', [$collection->id, $branch->node->id, 'branch', $branch->id]) }}" rel="tooltip" title="Edit" class="btn btn-mini"><i class="icon-edit"></i></a>
                            @endif
                                
                            @if (Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.hierarchy-management'))
                                <a href="#" rel="tooltip" title="Add Link" class="btn btn-mini open-node-modal"><i class="icon-link"></i></a>
                                <a href="#" rel="tooltip" title="Remove Link" class="btn btn-mini open-remove-link-modal"><i class="icon-unlink"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="dd-handle">
                        {{ $branch->node->title }}
                    </div>

                    @if (count($branch->getChildren()))
                        @include('nodes.branch', array('branches' => $branch->getChildren(), 'nodeTypes' => $nodeTypes))
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
            <p>Please select a node type to place in the hierarchy.</p>
            {{ Form::select('node_type', NodeType::forSelect($collection, true, 'create'), null, array('id' => 'node_type_select', 'class' => 'select2'))}}
            <div id="addNodeModalExisting" style="display: none;">
                {{ Form::hidden('existing_node', null, array('id' => 'existing_node_select')) }}
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="addNodeConfirm">Add Node</a>
        </div>
    </div>

    <div class="modal hide fade" id="deleteLinkModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this link?</p>
            <div class="well">
                <p><strong>This will also&hellip;</strong></p>
                <ul>
                    <li>Remove all children <em>links</em> of this node (if it has any). You will be able to find them in the list view.</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="deleteLinkConfirm">Yes, I'm Sure</a>
        </div>
    </div>

@stop