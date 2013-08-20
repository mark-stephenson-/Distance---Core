<?php
    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }

    $catalogue = Catalogue::find($column->catalogue);
?>

<textarea class="html-editor" name="nodetype[{{ $column->name }}]" id="input_{{ $column->name }}">{{ Input::old('nodetype.' . $column->name, $value) }}</textarea>

<a href="#{{ $column->name }}-resource_window" data-toggle="modal" style="display: none" data-dest="html" id="input_{{ $column->name }}_resource_link" data-resource="input_{{ $column->name }}" class="resource_fancybox">Choose One</a>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif

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
                                <img src="/file/{{ $resource->filename }}?type=view" alt="" style="max-width: 24px; max-height: 24px;" />
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
                            <a href="#" data-id="{{ $resource->id }}" data-filename="{{ $resource->filename }}" @if ( $resource->isImage() ) data-image="true" @endif>Use</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    
    var editor = $( '.html-editor' ).ckeditor();
    editor.ckeditorGet().config.baseHref = "{{ URL::to('file') }}/{{ $collection->id }}/";

    $("#{{ $column->name }}-resource_window").on('shown', function() {
        $("#{{ $column->name }}-resource_window a").click( function(e) {
            e.preventDefault();
            
            var html = '';

            if ( $(this).attr('data-image') == "true") {
                html = "<img src='" + $(this).attr('data-filename') + "' />";
            } else {                
                html = "<a href='" + $(this).attr('data-filename') + "'>" + $(this).attr('data-filename') + "</a>";
            }

            CKEDITOR.instances['input_{{ $column->name }}'].insertHtml(html);

            $("#{{ $column->name }}-resource_window").modal('hide');
        });
    });
</script>