@extends('layouts.master')

@section('header')
    <h1>Questionnaires</h1>
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
                    url: "{{ action('NodesController@updateOrder', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}",
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
                url: '{{ route('nodes.lookup', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}',
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term,
                        c: {{ CORE_COLLECTION_ID }}
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

            var url = "{{ route('nodes.unlink', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}/" + currentNodeId;
            window.location = url;

        });

        $('#addNodeConfirm').on('click', function(e) {

            var nodeType = $('#node_type_select').val();

            if (nodeType != 'existing') {
                var url = "{{ route('questionnaires.create') }}/" + nodeType + '/' + currentNodeId;
            } else {
                var nodeId = $('#existing_node_select').val();

                if (nodeId) {
                    var url = "{{ route('nodes.link', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}/" + nodeId + '/' + currentNodeId;
                } else {
                    alert('nope');
                }
            }
            window.location = url;

        });

        var publishQuestionSetConfirmUrl = '';

        $('.open-publish-question-set-modal').on('click', function(e) {
            e.preventDefault();

            publishQuestionSetConfirmUrl = $(e.currentTarget).attr('href');

            $('#publishQuestionSetModal').modal('show');
        });

        $('#publishQuestionSetConfirm').on('click', function(e) {

            e.preventDefault();

            window.location = publishQuestionSetConfirmUrl;

        });
    </script>
@stop

@section('body')

     <div class="modal hide fade" id="publishQuestionSetModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to publish this question set?</p>
            <div class="well">
                <p><strong>This will also&hellip;</strong></p>
                <ul>
                    <li>Retire the current published question set and all of it's questions.</li>
                    <li>Prevent users from answering the old question set on the app.</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="publishQuestionSetConfirm">Yes, I'm Sure</a>
        </div>
    </div>

    <div class="btn-group pull-right">
        <a href="{{ route('nodes.list', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}" class="btn"><i class="icon-list"></i> Node List</a>

        {{--@if ( count(NodeType::forSelect($collection, false, 'create')) )--}}
            {{--<a href="#" class="btn" id="openRootNodeModal"><i class="icon-plus"></i> New Root Node</a>--}}
        {{--@endif--}}
    </div>
    
    <div class="dd">
        <ol class="dd-list">
            @foreach ($branches->getChildren() as $branch)
                <li class="dd-item" data-id="{{ $branch->id }}">
                    <div class="pull-right node-hierarchy-buttons">
                        <small class="muted"><em>{{ $nodeTypes[$branch->node->node_type]->label }}</em></small> {{ $branch->node->statusBadge }}
                        <div class="btn-group">
                            <a href="{{ route('questionnaires.view', array($branch->node->id, 'branch', $branch->id)) }}" rel="tooltip" title="View" class="btn btn-mini"><i class="icon-search"></i></a>
                            @if ($branch->node->status == 'published')
                                <a href="{{ route('questionnaires.create-revision', array($branch->node->id, 'branch', $branch->id)) }}" class="btn btn-mini">Create Revision</a>
                            @elseif ($branch->node->status == 'draft')
                                <a href="{{ route('questionnaires.edit', array($branch->node->id, 'branch', $branch->id)) }}" rel="tooltip" title="Edit" class="btn btn-mini"><i class="icon-edit"></i></a>
                                <a href="{{ route('questionnaires.publish-revision', array($branch->node->id, 'branch', $branch->id)) }}" class="btn btn-mini open-publish-question-set-modal">Publish Revision</a>
                                <a href="{{ route('questionnaires.create', array('1', $branch->node->id)) }}" rel="tooltip" title="Add Link" class="btn btn-mini"><i class="icon-link"></i></a>
                                <a href="#" rel="tooltip" title="Remove Link" class="btn btn-mini open-remove-link-modal"><i class="icon-unlink"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="dd-handle">
                        {{ $branch->node->title }}
                    </div>

                    @if (count($branch->getChildren()))
                        @include('questionnaires.questions', array('branches' => $branch->getChildren(), 'nodeTypes' => $nodeTypes))
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    <div class="modal hide fade" id="addNodeModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add Question</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to add a new question?</p>
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