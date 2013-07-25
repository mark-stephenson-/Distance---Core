@extends('layouts.master')

@section('header')
    <h1>{{ $node->title }}</h1>
@stop

@section('body')

    <div class="btn-group pull-right">
        @if (Sentry::getUser()->hasAccess('nodes.edit'))
            <a href="{{ route('nodes.edit', [$node->id, $revisionData->id]) }}" class="btn"><i class="icon-level-up"></i> Publish</a>
            <a href="{{ route('nodes.edit', [$node->id, $revisionData->id]) }}" class="btn"><i class="icon-level-down"></i> Retire</a>
            <a href="{{ route('nodes.edit', [$node->id, $revisionData->id]) }}" class="btn"><i class="icon-key"></i> Permissions</a>
        @endif
        @if (Sentry::getUser()->hasAccess('nodes.edit'))
            <a href="{{ route('nodes.edit', [$node->id, $revisionData->id]) }}" class="btn"><i class="icon-edit"></i> Edit</a>
        @endif
    </div>

    <p class="lead">
        Revision #{{ $revisionData->id }}: {{ date('j F, Y @ H:i', strtotime($revisionData->updated_at)) }} - 
        <small>{{ $revisionData->user->fullName }}</small>
    </p>

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
                    <a href="{{ route('nodes.view', array($node->id, $revision->id, $branch->id)) }}" class="js-confirm_leave">
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

@stop