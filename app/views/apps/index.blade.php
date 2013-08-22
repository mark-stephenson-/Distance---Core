@extends('layouts.master')

@section('header')
    <h1>Apps</h1>
@stop

@section('body')

    @if (Sentry::getUser()->isSuperUser())
    <p class="pull-right">
        <a href="{{ route('apps.create') }}" class="btn"><i class="icon-plus"></i> New App</a>
    </p>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>API Key</th>
                <th>Collections</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apps as $app)
                <?php
                    $collectionAccess = array();
                    foreach($app->collections as $collection) {
                        $collectionAccess[] = 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.*';
                    }
                ?>
                @if (Sentry::getUser()->hasAnyAccess($collectionAccess))
                    <tr>
                        <td>
                            {{ $app->name }}
                        </td>
                        <td>
                            {{ $app->api_key }}
                        </td>
                        <td>
                            @foreach($app->collections as $collection)
                                @if (Sentry::getUser()->hasAccess('cms.apps.' . $app->id . '.collections.' . $collection->id . '.*'))
                                    {{ $collection->name }}<br />
                                @endif
                            @endforeach
                        </td>
                        <td width="250">
                            <a href="{{ route('collections.index', array($app->id)) }}" class="btn btn-small"><i class="icon-th-large"></i> Collections</a>
                            @if (Sentry::getUser()->isSuperUser())
                                <a href="{{ route('apps.edit', array($app->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                                <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

@stop