<?php
    
    $identifier = uniqid();
    $language = "en";

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

<div id="{{ $identifier }}" class="resource-{{ $column->name }}-container resource-view">
    {{ Form::hidden('nodetype['. $column->name .']', @$data->{$column->name}, array('id' => 'nodetype-'. $column->name)) }}
    {{ Form::select("language", Config::get("languages.list"), 'en', array("class" => "child-select", "style" => "margin:0 4px 4px 0")) }}
    <i class="icon-globe" data-toggle="tooltip" title="Toggle localisation of this category."></i>
    @if ($resource)
        <div class="resource" lang="{{ $language }}">
            <div class="image">
                @if ( $resource->isImage() )
                    <img src="{{ $resource->path($language) }}?type=view" alt="" lang="{{ $language }}"/>
                @else
                    <i class="icon-file"></i>
                @endif
            </div>

            <a href="{{ route('resources.localisations', array($appId, $collectionId, $catalogue->id, $resource->id, 'en')) }}" target="new">
                {{ $resource->filename }}
            </a>
            <p class="links">
                <a href="javascript:void(0)" class="change-resource">Change</a> | 
                <a href="javascript:void(0)" class="remove-resource">Remove</a>
            </p>
        </div>
    @else
        <div id="{{ $identifier }}" class="resource" lang="{{ $language }}">
            <p style="padding-top: 5px;">No Resource Selected.</p>
            <p class="links">
                <a href="javascript:void(0)" class="choose-resource">Choose One</a>
            </p>
        </div>
    @endif
</div>
<!-- /.resource-container -->

<div class="modal hide fade {{ $identifier }}" id="{{ $column->name }}-resource_window">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Resources <small>Catalogue: {{ $catalogue->name }}</small></h3>
    </div>
    
    {{ Form::select("language", Config::get("languages.list"), 'en', array("class" => "child-select", "style" => "margin: 20px 20px 0 20px")) }}
    
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
                                <img src="{{ $resource->path($language) }}?type=view" alt="" style="max-width: 24px; max-height: 24px;" lang="{{ $language }}" />
                            @else
                                <i class="icon-file"></i>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('resources.localisations', array($appId, $collectionId, $catalogue->id, $resource->id, $language)) }}" target="new">
                                {{ substr($resource->filename, 0, 50) }}
                            </a>
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
                            <a href="javascript:void(0)" class="use-resource" data-id="{{ $resource->id }}" data-path="{{ $resource->path($language) }}" data-filename="{{ $resource->filename }}" 
                               @if ( $resource->isImage() ) data-image="true" @endif> Use
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="empty-{{ $column->name}}-resource" style="display:none">
    <div class="resource">
        <div class="image"></div>

        <a href="{{ route('resources.localisations', array($appId, $collectionId, $catalogue->id, $resource->id, 'en')) }}" target="new">
            {{ $resource->filename }}
        </a>
        <p class="links">
            <a href="javascript:void(0)" class="change-resource">Change</a> | 
            <a href="javascript:void(0)" class="remove-resource">Remove</a>
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
            if (response.success == true ) {
                var file = response.data;
                var path = '{{ url() }}/file/' + file.collection_id + '/en/' + file.filename;
                var append = '<tr><td>';

                if (file.mime.substring(0,6) == "image/") {
                    append += '<img src="' + path + '?type=view" alt="" style="max-width: 24px; max-height: 24px;" />';
                    var image = 'data-image="true"';
                } else {
                    append += '<i class="icon-file"></i>';
                    var image = 'data-image="false"';
                }

                append += '</td><td>' + file.filename + '</td><td>';

                if (file.sync) {
                    append += '<i class="icon-ok"></i>';
                } else {
                    append += '<i class="icon-remove"></i>';
                }
                append += '</td><td><a href="javascript:void(0)" class="use-resource" data-id="' + file.id + '" data-path="' + path + '" data-filename="' + file.filename + '"' + image + '>';
                append += 'Use</a></td></tr>';

                $("#{{ $column->name }}-resource_window").find('table tbody').append(append);
            }
        }, [
            @if ($catalogue->restrictions)
                {title : "Allowed Files", extensions : "{{ implode(',', $catalogue->restrictions) }}"},
            @endif
        ], {
            fileSize: '{{ Config::get('core.prefrences.file-upload-limit') }}'
        });
    
        function buttonActions() {
            
            $(".choose-resource, .change-resource").click(function() {
                $("#{{ $column->name }}-resource_window").modal('show');
                buttonActions();
            });

            $(".remove-resource").click(function() {
                var html = '<p style="padding-top: 5px;">No Resource Selected.</p><p class="links"><a href="javascript:void(0)" class="choose-resource">Choose One</a></p>';
                $(this).closest('.resource').html(html);
                $('#nodetype-{{ $column->name }}').val('');
                buttonActions();
            });
        }
    
    $(function(){ buttonActions();
        
        $("#{{ $column->name }}-resource_window a.use-resource").click(function(e) {
            $("#nodetype-{{ $column->name }}").val($(this).attr('data-id'));
            $(".resource-{{ $column->name}}-container .resource").remove();
            $(".resource-{{ $column->name }}-container").append( $('.empty-{{ $column->name }}-resource').html() );
            $(".resource-{{ $column->name }}-container .resource .filename").html( $(this).attr('data-filename') );

            if ($(this).attr('data-image') == "true") {
                var lang = $('#{{ $identifier }} select[name=language].child-select').val();
                var src = "<img src='" + $(this).attr('data-path') + "?type=view' lang='" + lang + "'/>";
                $(".resource-{{ $column->name }}-container .resource .image").html(src);
            }
            $("#{{ $column->name }}-resource_window").modal('hide'); buttonActions();
        });
        
        $('#{{ $identifier }} select[name=language].child-select').change(function() {
            if ($("#{{ $identifier }} .resource .image img").length) {
                var haystack = $("#{{ $identifier }} .resource .image img").attr('src');
                var needle = '/' + $("#{{ $identifier }} .resource .image img").attr('lang') + '/';
                var replacement = '/' + $(this).val() + '/';
                
                var src = haystack.replace(needle, replacement);

                $("#{{ $identifier }} .resource .image img").attr('src', src);
                $("#{{ $identifier }} .resource .image img").attr('lang', $(this).val());
                
                $("{{ $column->name }}-resource_window .modal-body img").attr('src', src);
                $("{{ $column->name }}-resource_window .modal-body img").attr('lang', $(this).val());
            }
        });
                 
        $("#{{ $column->name }}-resource_window.{{ $identifier }} select[name=language].child-select").change(function(){
            $("#{{ $identifier }}.resource-{{ $column->name }}-container select[name=language]").val($(this).val());
            $("#{{ $identifier }}.resource-{{ $column->name }}-container select[name=language]").change();
            
            var child_select = $("#{{ $column->name }}-resource_window.{{ $identifier }} select[name=language].child-select");
            var replace = '/' + child_select.val() + '/';
            
            $("#{{ $column->name }}-resource_window.{{ $identifier }} .modal-body .use-resource").each(function(){
                var haystack = $(this).attr('data-path');
                var needle = '/' + $("#{{ $column->name }}-resource_window.{{ $identifier }} .modal-body img").attr('lang') + '/';
                var data_path = haystack.replace(needle, replace);
                
                $(this).attr('data-path', data_path);
            });
            
            $("#{{ $column->name }}-resource_window.{{ $identifier }} .modal-body img").each(function(){
                var haystack = $(this).attr('src');
                var needle = '/' + $(this).attr('lang') + '/';
                var src = haystack.replace(needle, replace);

                $(this).attr('src', src);
                $(this).attr('lang', child_select.val());
            });
        });
    });
    
</script>

@endif