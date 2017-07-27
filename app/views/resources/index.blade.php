@extends('layouts.master')

@section('header')
    <h1>Resources</h1>
@stop

@section('body')

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Filetypes</th>
                <th>Files</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catalogues as $catalogue)
                <?php
                    $catalogueAccess = array();
                    $catalogueAccess[] = 'cms.apps.' . CORE_APP_ID . '.collections.' . CORE_COLLECTION_ID .'.catalogues.create';
                    $catalogueAccess[] = 'cms.apps.' . CORE_APP_ID . '.collections.' . CORE_COLLECTION_ID .'.catalogues.update';
                    $catalogueAccess[] = 'cms.apps.' . CORE_APP_ID . '.collections.' . CORE_COLLECTION_ID .'.catalogues.delete';
                    $catalogueAccess[] = 'cms.apps.' . CORE_APP_ID . '.collections.' . CORE_COLLECTION_ID .'.catalogues.' . $catalogue->id . '.*';
                ?>
                @if (Sentry::getUser()->hasAnyAccess($catalogueAccess))
                    <tr>
                        <td>
                            {{ $catalogue->name }}
                        </td>
                        <td>
                            @if (count($catalogue->restrictions))
                                <span class="label">{{ implode('</span> <span class="label">', $catalogue->restrictions) }}</span>
                            @else
                                No Restrictions
                            @endif
                        </td>
                        <td>
                            {{ $catalogue->resources()->count() }}
                        </td>
                        <td width="150">
                            <a href="{{ route('resources.show', array($appId, $collectionId, $catalogue->id, 'en')) }}" class="btn btn-small"><i class="icon-search"></i> View Resources</a>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

@stop