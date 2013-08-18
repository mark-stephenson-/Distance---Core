@extends('layouts.master')

@section('header')
    <h1>{{ $node->title }}</h1>
@stop

@section('js')

    <script>

        $('.open-publish-node-modal').on('click', function(e) {
            e.preventDefault();
            $('#nodePublishModal').modal('show');
        });

        $('#publishNodeConfirm').on('click', function(e) {

            e.preventDefault();

            var url = "{{ route('nodes.publish', [$collection->id, $node->id, $revisionData->id, $branch->id]) }}";
            window.location = url;

        });

        $('.open-retire-node-modal').on('click', function(e) {
            e.preventDefault();
            $('#nodeRetireModal').modal('show');
        });

        $('#retireNodeConfirm').on('click', function(e) {

            e.preventDefault();

            var url = "{{ route('nodes.retire', [$collection->id, $node->id, $revisionData->id, $branch->id]) }}";
            window.location = url;

        });

    </script>

@endsection

@section('body')

    <div class="btn-group pull-left">
        @if ( $lastView = Session::get('last-view') )
            @if ( $lastView['collection_id'] == $collection->id)
                <a href="{{ $lastView['url'] }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
            @else
                <a href="{{ route('nodes.list', $collection->id) }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
            @endif
        @else
            <a href="{{ route('nodes.list', $collection->id) }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
        @endif
    </div>

    <div class="btn-group pull-right">
        @if (Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.' . $nodeType->name . '.revision-management'))

            @if ($revisionData->status == 'draft')
                <a href="#" class="btn open-publish-node-modal"><i class="icon-level-up"></i> Publish</a>
            @endif

            @if ($revisionData->status == 'published')
                <a href="#" class="btn open-retire-node-modal"><i class="icon-level-down"></i> Retire</a>
            @endif
            
        @endif
        @if (Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.' . $nodeType->name . '.update'))
            <a href="{{ route('nodes.edit', [$collection->id, $node->id, $revisionData->id]) }}" class="btn"><i class="icon-edit"></i> Edit</a>
        @endif
    </div>

    <p class="lead" style="clear: both; padding-top: 15px;">
        Revision #{{ $revisionData->id }}: {{ date('j F, Y @ H:i', strtotime($revisionData->updated_at)) }} - 
        <small>{{ $revisionData->user->fullName }} - {{ ucfirst($revisionData->status) }}</small>
    </p>

    @if (count($breadcrumbs))
        <ul class="breadcrumb">

            <li><a href="{{ route('nodes.hierarchy', [$collection->id]) }}">{{ $collection->name }}</a> <span class="divider">/</span></li>

            @foreach($breadcrumbs as $crumb)
                <li><a href="{{ route('nodes.view', [$collection->id, $crumb->node->id, 'branch', $crumb->id]) }}">{{ $crumb->node->title }}</a> <span class="divider">/</span></li>
            @endforeach
            <li class="active">{{ $node->title }}</li>
        </ul>
    @endif

    <div class="well node-view">
        @foreach($nodeType->columns as $column)
            <section>
                <p><strong>{{ $column->label }}</strong></p>
                @include('nodecategories.view.' . $column->category, array('column' => $column, 'node' => $node, 'data' => $revisionData))
            </section>
        @endforeach
    </div>

    <p class="lead">Last 10 Revisions</p>
    <ul>
        @foreach($revisions as $revision)
            <li>
                @if ($revision->id != $revisionData->id)
                    <a href="{{ route('nodes.view', array($collection->id, $node->id, $revision->id, $branch->id)) }}" class="js-confirm_leave">
                @endif

                {{ date('j F, Y @ H:i', strtotime($revision->updated_at)) }} -
                {{ $revision->status }} - <small>{{ $revision->user->fullName }}.</small>

                @if ($revision->id != $revisionData->id)
                    </a>
                @else
                    <strong>(current)</strong>
                @endif
            </li>
        @endforeach
    </ul>

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

    <div class="modal hide fade" id="nodeRetireModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to retire this revision?</p>
            <div class="well">
                <p><strong>This will also&hellip;</strong></p>
                <ul>
                    <li>Make the node immediately unavailable on the application.</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="retireNodeConfirm">Yes, I'm Sure</a>
        </div>
    </div>

@stop