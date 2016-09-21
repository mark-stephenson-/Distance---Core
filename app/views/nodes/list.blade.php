@extends('layouts.master')

<?php
    function orderColumn($columnName) {
        $currentURL = URL::current();
        $queryString = array( 'filter' => Input::get('filter'), 'column' => $columnName );

        if ( $columnName == Input::get('column') ) {
            if ( Input::get('sort') == 'DESC') {
                $queryString['sort'] = 'ASC';
            } else {
                $queryString['sort'] = 'DESC';
            }
        } else {
            $queryString['sort'] = 'DESC';
        }

        return $currentURL . '?' . http_build_query($queryString);
    }
?>

@section('header')
    <h1>{{ $collection->name }}</h1>
@stop

@section('js')
    <style> table thead .cursor{ cursor: pointer; }</style>
    <script>
    
        var nodeToPublish = null;

        $('.open-publish-node-modal').on('click', function(e) {
            e.preventDefault();

            nodeToPublish = $(this);

            $('#nodePublishModal').modal('show');
        });

        $('#publishNodeConfirm').on('click', function(e) {

            e.preventDefault();

            window.location = nodeToPublish.attr('href');

        });

        $('#openNodeModal').on('click', function(e) {
            e.preventDefault();

            @if (Route::currentRouteName() == 'nodes.type-list')
                {{-- take them straight there! --}}
                var url = "{{ route('nodes.create', array($collection->application_id, $collection->id, $nodeType->id)) }}";

                window.location = url;

            @else
                $('#addNodeModal').modal('show');
            @endif
        });

        $('#addNodeConfirm').on('click', function(e) {

            var nodeType = $('#node_type_select').val();

            var url = "{{ route('nodes.create', array($collection->application_id, $collection->id)) }}/" + nodeType;

            window.location = url;

        });
    </script>
@stop

@section('body')

    <form class="form-inline pull-left">
        @if (Route::currentRouteName() !== 'nodes.type-list')
            <div class="btn-group change-collection">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    @if (Input::get('filter'))
                        {{ $nodeTypes[Input::get('filter')] }}
                    @else
                        Filter by Node Type
                    @endif
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu pull-right">
                    @foreach($nodeTypes as $id => $nodeType)
                        <li><a href="?filter={{ $id }}">{{ $nodeType }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>

    <p class="pull-right">

        @if (Route::currentRouteName() == 'nodes.type-list')
            <a href="{{ route('nodes.list', array($collection->application_id, $collection->id)) }}" class="btn"><i class="icon-list"></i> Node List</a>
        @endif

        @if (Config::get('core.features.hierarchy'))
            <a href="{{ route('nodes.hierarchy', array($collection->application_id, $collection->id)) }}" class="btn"><i class="icon-sitemap"></i> Hierarchy</a>
        @endif
        
        @if (count(NodeType::forSelect($collection, false, 'create')))
            <a href="{{ route('nodes.create', array($collection->application_id, $collection->id)) }}" class="btn" id="openNodeModal"><i class="icon-plus"></i> New Node</a>
        @endif
    </p>
    
    <table class="table">

        <thead>
            <tr>
                <th class="cursor"><a href="{{ orderColumn('id') }}">ID</a></th>
                <th class="cursor"><a href="{{ orderColumn('title') }}">Title</a></th>
                <th class="cursor"><a href="{{ orderColumn('node_type') }}">Node Type</a></th>
                <th class="cursor"><a href="{{ orderColumn('status') }}">Status</a></th>
                <th class="cursor">Owner</th>
                <th class="cursor"><a href="{{ orderColumn('created_at') }}">Created</a></th>
                <th width="150"></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($nodes as $node)
                @include('nodes.list-row', compact('node', 'nodeTypes'))
            @endforeach
        </tbody>
    </table>

    <div style="text-align: center">
        <?php echo $nodes->appends( array('filter' => Input::get('filter'), 'column' => Input::get('column'), 'sort' => Input::get('sort')) )->links(); ?>
    </div>

    <div class="modal hide fade" id="addNodeModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add Node</h3>
        </div>
        <div class="modal-body">
            <p>Please select a node type to add.</p>
            {{ Form::select('node_type', NodeType::forSelect($collection, false, 'create'), null, array('id' => 'node_type_select', 'class' => 'select2'))}}
            <div id="addNodeModalExisting" style="display: none;">
                {{ Form::hidden('existing_node', null, array('id' => 'existing_node_select')) }}
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="addNodeConfirm">Add Node</a>
        </div>
    </div>

    <div class="modal hide fade" id="nodePublishModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to publish this revision?</p>
            <div class="well">
                <p><strong>This will also&hellip;</strong></p>
                <ul>
                    <li>Make the newly published revision immediately available on the application.</li>
                    <li>Retire the current published revision if there is one.</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="publishNodeConfirm">Yes, I'm Sure</a>
        </div>
    </div>

    <div class="modal hide fade" id="deleteNodeModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this node?</p>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="do">Yes, I'm Sure</a>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            var application_id;
            var collection_id;
            var node_id;
            var branch_id;
            var latest_revision;

            $('.modal-toggle').click( function(e) {
                e.preventDefault();

                if ( $(this).attr('href') == "#deleteNodeModal" ) {
                    application_id = $(this).attr('data-application-id');
                    collection_id = $(this).attr('data-collection-id');
                    node_id = $(this).attr('data-node-id');
                    branch_id = $(this).attr('data-branch-id');
                    latest_revision = $(this).attr('data-latest-revision');

                    $('#deleteNodeModal').modal('show');
                }
            });

            $("#deleteNodeModal").on('shown', function() {
                 if ( branch_id === undefined ) {
                        var url = "{{ route('nodes.delete', array('appId', 'collectionId', 'nodeId')) }}";

                        $("#do").one('click', function(e) {
                            e.preventDefault();
                            $.ajax({
                                type: "POST",
                                data: 'latest_revision=' + latest_revision,
                                url: url.replace('appId', application_id).replace('collectionId', collection_id).replace('nodeId', node_id),
                                success: function() {
                                    location.reload(true);
                                }
                            });
                        });
                    } else {
                        // There is a branch ID
                    }
            });
        });
    </script>

@stop