@extends('layouts.master')

@section('header')
    <h1>Collections</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAnyAccess(array('cms.apps.' . $appId . '.collections.create', 'cms.apps.' . $appId . '.collection-management')))
            <a href="{{ route('collections.create', array($appId)) }}" class="btn"><i class="icon-plus"></i> New Collection</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Application</th>
                <th>API Key</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $collection)
                <tr>
                    <td>
                        {{ $collection->name }}
                    </td>
                    <td>{{ @$collection->application->name ?: '-' }}</td>
                    <td>
                        {{ $collection->api_key }}
                    </td>
                    <td width="330">
                        @if (Config::get('core.features.hierarchy'))
                            <a href="{{ route('nodes.hierarchy', array($collection->application_id, $collection->id)) }}" class="btn btn-small"><i class="icon-sitemap"></i> Hierarchy</a>
                        @endif
                        <a href="{{ route("nodes.list", array($collection->application_id, $collection->id)) }}" class="btn btn-small"><i class="icon-list"></i> Node List</a>

                        @if (Sentry::getUser()->hasAnyAccess(array('cms.apps.' . $appId . '.collections.' . $collection->id . '.update', 'cms.apps.' . $appId . '.collection-management')))
                            <a href="{{ route('collections.edit', array($collection->application_id, $collection->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                        @endif

                        @if (Sentry::getUser()->hasAnyAccess(array('cms.apps.' . $appId . '.collections.' . $collection->id . '.delete', 'cms.apps.' . $appId . '.collection-management')))
                            <a href="#deleteCollectionModal" class="btn btn-small modal-toggle" data-collection-id="{{ $collection->id }}"><i class="icon-trash"></i> Delete</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal hide fade" id="deleteCollectionModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this collection?</p>
        </div>
        <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn">Close</a>
            <a href="#" class="btn btn-primary" id="continue">Yes, I'm Sure</a>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $('.modal-toggle').click( function(e) {
                e.preventDefault();

                if ( $(this).attr('href') == "#deleteCollectionModal" ) {
                    var collection_id = $(this).attr('data-collection-id');
                    var url = '{{ route('collections.destroy', array($appId, 'collectionID')) }}';

                    url = url.replace('collectionID', collection_id);

                    $('#deleteCollectionModal #continue').attr('href', url);
                    $('#deleteCollectionModal').modal('show');
                }
            });
        });
    </script>

@stop