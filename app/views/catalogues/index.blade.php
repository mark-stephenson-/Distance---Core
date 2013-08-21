@extends('layouts.master')

@section('header')
    <h1>Catalogues</h1>
@stop

@section('body')

    <p class="pull-right">
        <a href="{{ route('catalogues.create', array($appId, $collectionId)) }}" class="btn"><i class="icon-plus"></i> New Catalogue</a>
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Restrictions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catalogues as $catalogue)
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
                <td width="150">
                    <a href="{{ route('catalogues.edit', array($appId, $collectionId, $catalogue->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" class="btn btn-small deleteModal" data-id="{{ $catalogue->id }}" data-name="{{ $catalogue->name }}"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade hide" id="deleteModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3></h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this catalogue? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes">Yes, Delete it.</a>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $(".deleteModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('catalogues.destroy', array($appId, $collectionId, 5)) }}'

                $("#deleteModal").find('h3').html( "Delete collection <small>" + data_name + "</small>");
                $("#deleteModal").find('.yes').attr('href', url.replace('id', data_id));

                $("#deleteModal").modal('show');
            });
        });
    </script>

@stop