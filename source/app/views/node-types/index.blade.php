@extends('layouts.master')

@section('header')
    <h1>Node Type</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('node-types.create'))
            <a href="{{ route('node-types.create') }}" class="btn"><i class="icon-plus"></i> New Node Type</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Label</th>
                <th>Code</th>
                <th>Collections</th>
                <th>Columns</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nodeTypes as $nodeType)
            <tr>
                <td>
                    {{ $nodeType->label }}
                </td>
                <td>
                    <code>{{ $nodeType->name }}</code>
                </td>
                <td>
                    @if (count($nodeType->collections))
                        @foreach($nodeType->collections as $collection)
                            {{ $collection->name }}<br />
                        @endforeach
                    @else
                        No Collections
                    @endif
                </td>
                <td>
                    @if (count($nodeType->columns))
                        @foreach($nodeType->columns as $column)
                            {{ $column->label }} <em class="muted">{{ Config::get('node-categories.' . $column->category . '.label') }}</em><br />
                        @endforeach
                    @else
                        No Columns
                    @endif
                </td>
                <td width="130">
                    <a href="{{ route('node-types.edit', array($nodeType->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" class="btn btn-small deleteModal" data-id="{{ $nodeType->id}}" data-name="{{ $nodeType->label }}"><i class="icon-trash"></i> Delete</a>
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
            <p>Are you sure you want to delete this node type? This cannot be undone.</p>
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
                var url = '{{ route('node-types.destroy', array('id')) }}';

                $("#deleteModal").find('h3').html( "Delete Node Type <small>" + data_name + "</small>");
                $("#deleteModal").find('.yes').attr('href', url.replace('id', data_id));

                $("#deleteModal").modal('show');
            });
        });
    </script>

@stop