<?php

    if ($data) {
        $resource = Resource::find(@$data->{$column->name});
    } else {
        $resource = null;
    }

    if (!$resource and Input::old('nodetype.'. $column->name)) {
        $resource = Resource::find(Input::old('nodetype.'. $column->name));
    }
?>

@if (!isset($column->catalogue) or !isset($column->catalogue->{CORE_COLLECTION_ID}))
    
    <p>A catalogue has not yet been assigned to this field - please contact an adminstrator</p>

@else

<?php

    $catalogue = Catalogue::find($column->catalogue->{CORE_COLLECTION_ID});

?>

<div class="resource-{{ $column->name }}-container resource-view">
    {{ Form::hidden('nodetype['. $column->name .']', @$data->{$column->name}, array('id' => 'nodetype-'. $column->name)) }}

    @if ($resource)
        <div class="resource">
            <div class="image">
                @if ( $resource->isImage() )
                    <img src="{{ $resource->path() }}?type=view" alt="" />
                @else
                    <i class="icon-file"></i>
                @endif
            </div>

            <p class="filename">{{ $resource->filename }}</p>
            <p class="links">
                <a href="#{{ $column->name }}-resource_window" data-toggle="modal">Change</a> | 
                <a href="#" class="remove-resource">Remove</a>
            </p>
        </div>
    @else
        <div class="resource">
            <p style="padding-top: 5px;">No Resource Selected.</p>
            <p class="links">
                <a href="#{{ $column->name }}-resource_window" data-toggle="modal">Choose One</a>
            </p>
        </div>
    @endif
</div>
<!-- /.resource-container -->

<div class="modal hide fade" id="{{ $column->name }}-resource_window">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Resources <small>Catalogue: {{ $catalogue->name }}</small></h3>
    </div>
    
    <div class="upload-container">
        <input type="file" multiple="multiple" name="upload" class="file_upload_fallback" id="file_upload_fallback" style="display: block; width: 0px; height: 0px;" />

        <div class="well well-small" id="dropzone" style="text-align: center; margin: 20px 20px 0 20px">
            <span class="muted">Click (or drag) files here to upload.</pspan>
        </div>
    </div>

    <div class="modal-body">
        <table class="table" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th></th>
                    <th style="width: 420px !important; max-width: 420px;">Filename</th>
                    <th>Sync</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach( $catalogue->resources as $resource )
                    <tr>
                        <td>
                            @if ( $resource->isImage() )
                                <img src="{{ $resource->path() }}?type=view" alt="" style="max-width: 24px; max-height: 24px;" />
                            @else
                                <i class="icon-file"></i>
                            @endif
                        </td>
                        <td>
                            {{ substr($resource->filename, 0, 50) }}
                            @if (strlen($resource->filename) >= 50)
                                &hellip;
                            @endif
                        </td>
                        <td>
                            @if ( $resource->sync )
                                <i class="icon-ok"></i>
                            @else
                                <i class="icon-remove"></i>
                            @endif
                        </td>
                        <td>
                            <a href="#" data-id="{{ $resource->id }}" data-path="{{ $resource->path() }}" data-filename="{{ $resource->filename }}" @if ( $resource->isImage() ) data-image="true" @endif>Use</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="empty-{{ $column->name}}-resource" style="display: none">
    <div class="resource">
        <div class="image"></div>

        <p class="filename"></p>
        <p class="links">
            <a href="#{{ $column->name }}-resource_window" data-toggle="modal">Change</a> | 
            <a href="#" class="remove-resource">Remove</a>
        </p>
    </div>
</div>

<script>
        loadResourceUploader('{{ route('resources.process', array($appId, $collection->id, $catalogue->id, 'en')) }}', function(response) {
                $.ajax({
                    url: '{{ route('collections.createResourceArchive', array($appId, $collection->id)) }}',
                });
        }, function(response) {
            response = $.parseJSON(response.response);

            if ( response.success == true ) {
                var file = response.data;

                var path = '{{ url() }}/file/' + file.collection_id + '/' + file.filename;

                var append = '<tr><td>';

                if ( file.mime.substring(0,6) == "image/" ) {
                    append += '<img src="' + path + '?type=view" alt="" style="max-width: 24px; max-height: 24px;" />';
                    var image = 'data-image="true"';
                } else {
                    append += '<i class="icon-file"></i>';
                    var image = 'data-image="false"';
                }

                append += '</td><td>' + file.filename + '</td><td>';

                if ( file.sync ) {
                    append += '<i class="icon-ok"></i>';
                } else {
                    append += '<i class="icon-remove"></i>';
                }

                append += '</td><td><a href="#" data-id="' + file.id + '" data-path="' + path + '" data-filename="' + file.filename + '"' + image + '>Use</a></td></tr>';

                $("#{{ $column->name }}-resource_window").find('table tbody').append(append);
            }
        }, [
            @if ($catalogue->restrictions)
                {title : "Allowed Files", extensions : "{{ implode(',', $catalogue->restrictions) }}"},
            @endif
        ], {
            fileSize: '{{ Config::get('core.prefrences.file-upload-limit') }}'
        });

    $(document).on('click', '.remove-resource', function() {
        var parent = $(this).closest('.resource');
        parent.html('<p style="padding-top: 5px;">No Resource Selected.</p><p class="links"><a href="#{{ $column->name }}-resource_window" data-toggle="modal">Choose One</a></p>');
        $('#nodetype-{{ $column->name }}').val('');
    });

    $("#{{ $column->name }}-resource_window").on('shown', function() {
        $("#{{ $column->name }}-resource_window").on( 'click', 'a', function(e) {
            e.preventDefault();

            $("#nodetype-{{ $column->name }}").val( $(this).attr('data-id') );
            $(".resource-{{ $column->name}}-container .resource").remove();
            $(".resource-{{ $column->name }}-container").prepend( $('.empty-{{ $column->name }}-resource').html() );

            $(".resource-{{ $column->name }}-container .resource .filename").html( $(this).attr('data-filename') );

            if ( $(this).attr('data-image') == "true") {
                $(".resource-{{ $column->name }}-container .resource .image").html( '<img src="' + $(this).attr('data-path') +'?type=view" />' );
            }
            $("#{{ $column->name }}-resource_window").modal('hide');
        });
    });
</script>

@endif