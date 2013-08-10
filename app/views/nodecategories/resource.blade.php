<?php

    if ($data) {
        $resource = Resource::find(@$data->{$column->name});
    } else {
        $resource = null;
    }

    $catalogue = Catalogue::find($column->catalogue);
?>

<div class="resource-{{ $column->name }}-container resource-view">
    {{ Form::hidden('nodetype['. $column->name .']', null, ['id' => 'nodetype-'. $column->name]) }}

    @if ( $resource )
        <div class="resource">
            <div class="image">
                @if ( $resource->isImage() )
                    <img src="/file/{{ $resource->filename }}" alt="" />
                @else
                    <i class="icon-file"></i>
                @endif
            </div>

            <p class="filename">{{ $resource->filename }}</p>
            <a href="#{{ $column->name }}-resource_window" data-toggle="modal">Change</a>
        </div>
    @else
        <div class="no-resource">
            <p style="padding-top: 5px;">No Resource Selected.</p>
            <a href="#{{ $column->name }}-resource_window" data-toggle="modal">Choose One</a>
        </div>
    @endif
</div>
<!-- /.resource-container -->

<div class="modal hide fade" id="{{ $column->name }}-resource_window">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Resources <small>Catalogue: {{ $catalogue->name }}</small></h3>
    </div>

    <div class="modal-body">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Filename</th>
                    <th>Description</th>
                    <th>Sync</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach( $catalogue->resources as $resource )
                    <tr>
                        <td>
                            @if ( $resource->isImage() )
                                <img src="/file/{{ $resource->filename }}" alt="" style="max-width: 24px; max-height: 24px;" />
                            @else
                                <i class="icon-file"></i>
                            @endif
                        </td>
                        <td>{{ $resource->filename }}</td>
                        <td>{{ $resource->description }}</td>
                        <td>
                            @if ( $resource->sync )
                                <i class="icon-ok"></i>
                            @else
                                <i class="icon-remove"></i>
                            @endif
                        </td>
                        <td>
                            <a href="#" data-id="{{ $resource->id }}" data-filename="{{ $resource->filename }}">Use</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $("#{{ $column->name }}-resource_window").on('shown', function() {
        $("#{{ $column->name }}-resource_window a").click( function(e) {
            e.preventDefault();

            $("#nodetype-{{ $column->name }}").val( $(this).attr('data-id') );
            $(".resource-{{ $column->name}}-container").find('.no-resource').hide();
            $("#{{ $column->name }}-resource_window").modal('hide');
        });
    });
</script>