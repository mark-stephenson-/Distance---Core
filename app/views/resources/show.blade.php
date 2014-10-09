@extends('layouts.master')

@section('header')
    <h1>Resources &#8227; {{ $catalogue->name }}</h1>
@stop
<?php $language = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1); ?>
@section('body')
    <div class="btn-group pull-left">
        <a href="{{ route('resources.index', array($appId, $collectionId)) }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
    </div>
    <div class="btn-group pull-right">
        <div class="upload_container">
            <input type="file" multiple="multiple" name="upload" class="file_upload_fallback" id="file_upload_fallback" style="display: none;">
            <button id="dropzone" class="btn"><i class="icon-upload"></i> Upload a New <b>{{ Config::get("languages.list")[$language] }}</b> Resource</button>
        </div>
    </div>
    <div class="btn-group pull-right">
        {{ Form::select("language", Config::get("languages.list"), $language) }}
    </div>
    <div class="progress" id="upload_progress">
      <div class="bar" style="width: 0%;"></div>
    </div>

    <table class="table table-striped resource_table">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Localisations</th>
                <th>Sync</th>
                <th>Public</th>
                <th>Localisation Actions</th>
                <th>Resource Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $resources = array_pluck($catalogue->resources, 'localisations');

                $localisations = array_map(function($resource){
                    return array_map(function($localisation){
                        return $localisation['lang'];
                    }, $resource->toArray());
                }, $resources);
                
                $languages = array_intersect_key(Config::get("languages.list"), array_flip(array_unique(array_flatten($localisations))));
            ?>
            @foreach($catalogue->resources as $resource)
            <tr>
                <td style="width: 80px !important; padding-right: 30px; height: 50px  !important; overflow: hidden; vertical-align: middle; text-align: center;">
                    @if ($resource->isImage($language))
                        <a href="{{ $resource->path($language) }}" @if ($resource->isPdf($language)) data-fancybox-type="iframe" @endif class="fancybox">
                            <img style="max-width: 80px;" src="{{ $resource->path($language) }}?type=view" width="80" height="50" />
                        </a>
                    @elseif (!file_exists($resource->systemPath($language)))
                        None
                    @else
                        <i class="icon-file"></i>
                    @endif
                </td>
                <td class="resource-name">
                    <a href="{{ $resource->path($language) }}" target="new">{{ $resource->displayText }}</a>
                    {{ Form::hidden("file-name", $resource->displayText, array('style' => 'width: 90%')) }}
                </td>
                <td id="{{ $resource->id }}">
                    {{ Form::select("language", array("" => "") + $languages, $language, array("style" => "margin:0 4px 4px")) }} 
                    <i class="icon-exclamation-sign" style="display:none" data-toggle="tooltip" title="&#9888; Resource is missing localisations"></i>
                    <script>
                        $("td#{{ $resource->id }} select[name=language] option").first().attr("disabled", true);
                        $("td#{{ $resource->id }} select[name=language]").children().each(function(){
                            if (this.value && !({{ json_encode(array_pluck($resource->localisations, "lang")) }}.indexOf(this.value) >= 0)) {
                                $(this).html("&#9888; " + $(this).html());
                                $("td#{{ $resource->id }} i").show();                            
                            }
                        });
                    </script>
                </td>
                <td>
                    @if ($resource->sync)
                        <a href="" class="btn toggle-sync" data-id="{{ $resource->id }}" data-sync="0"><i class="icon-ok"></i></a>
                    @else
                        <a href="" class="btn toggle-sync" data-id="{{ $resource->id }}" data-sync="1"><i class="icon-remove"></i></a>
                    @endif
                </td>
                <td>
                    @if ($resource->public)
                        <a href="" class="btn toggle-pub" data-id="{{ $resource->id }}" data-pub="0"><i class="icon-ok"></i></a>
                    @else
                        <a href="" class="btn toggle-pub" data-id="{{ $resource->id }}" data-pub="1"><i class="icon-remove"></i></a>
                    @endif
                </td>
                <td>
                    <a href="{{ route('resources.localisations', array($appId, $collectionId, $catalogue->id, $resource->id, $language)) }}" class="btn btn-small">
                        <i class="icon-search"></i> View Localisations
                    </a>
                    <a href="javascript:void(0)" class="btn btn-small uploadLocalisationModal" data-id="{{ $resource->id }}" data-name="{{ $resource->filename }}">
                        <i class="icon-refresh"></i> Upload
                    </a>
                    <a href="javascript:void(0)" class="btn btn-small deleteLocalisationModal" data-id="{{ $resource->id }}" data-name="{{ $resource->filename }}">
                        <i class="icon-trash"></i> Delete
                    </a>
                </td>
                <td>
                    <a href="javascript:void(0)" class="btn btn-small editNameModal" data-id="{{ $resource->id }}" data-name="{{ $resource->filename }}">
                        <i class="icon-edit"></i> Edit Name
                    </a>
                    <a href="javascript:void(0)" class="btn btn-small deleteResourceModal" data-id="{{ $resource->id }}" data-name="{{ $resource->filename }}">
                        <i class="icon-trash"></i> Delete
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="modal fade hide" id="deleteResourceModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3></h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this resource including all of it's localisations? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes">Yes, Delete it.</a>
        </div>
    </div>
    
    <div class="modal fade hide" id="editNameModal">
        {{ Form::open(array('enctype' => 'multipart/form-data', 'url' => route('resources.editName', array($appId, $collection->id, 'id', $language)), 'style' => 'margin-bottom: 0px;')) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Edit resource name</h3>
            </div>
            <div class="modal-body">
                <p>Editing the name of <b id="file-name"></b> will rename all localisations of the resource.</p>
                {{ Form::text("file-name", 'display_text', array("style" => "width:95%")) }}
                {{ Form::hidden('resource_id', null, array('id' => 'resource_id')) }}
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <input class="btn btn-primary" type="submit" value="Rename" />
            </div>
        {{ Form::close() }}
    </div>

    <div class="modal fade hide" id="deleteLocalisationModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3></h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the <b><u id="language-name"></u></b> localisation of <b id="file-name"></b>? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes">Yes, Delete it.</a>
        </div>
    </div>

    <div class="modal fade hide" id="uploadLocalisationModal">
        {{ Form::open(array('enctype' => 'multipart/form-data', 'url' => route('resources.updateFile', array($appId, $collection->id, 'id', $language)), 'style' => 'margin-bottom: 0px;')) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3></h3>
            </div>
            <div class="modal-body">
                <p>Uploading a new <b><u id="language-name"></u></b> version of <b id="file-name"></b> will overwrite the previous version.</p>
                {{ Form::select("language", Config::get("languages.list"), $language) }}
                {{ Form::hidden('resource_id', null, array('id' => 'resource_id')) }}
                {{ Form::file('file') }}
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <input class="btn btn-primary" type="submit" value="Upload new version" />
            </div>
        {{ Form::close() }}
    </div>

@stop

@section('js')

    <script>

        $(".fancybox").fancybox({
            padding: 0,
            iframe : {
                preload: false
            }
        });

        $("#upload_progress").hide();

        loadResourceUploader('{{ route('resources.process', array($appId, $collection->id, $catalogue->id, $language)) }}', function() {
            $.ajax({
                url: '{{ route('collections.createResourceArchive', array($appId, $collection->id)) }}',
                success: function() {
                    document.location.reload(true);
                }
            });
        }, function() {}, [
            @if ($catalogue->restrictions)
                {title : "Allowed Files", extensions : "{{ implode(',', $catalogue->restrictions) }}"},
            @endif
        ], {
            fileSize: '{{ Config::get('core.prefrences.file-upload-limit') }}'
        });
        
        $(".btn-group select[name=language], td select[name=language]").change(function() {
            var href = "{{ route('resources.show', array($appId, $collectionId, $catalogue->id, 'xx')) }}";
            window.location.href = href.substring(0, href.lastIndexOf('/') + 1) + $(this).val();
        });
        
        $("#uploadLocalisationModal select[name=language]").change(function() {
            $("#uploadLocalisationModal #language-name").text({{ json_encode(Config::get("languages.list")) }}[$(this).val()]);
        }).change();
        
        $(".toggle-sync").click( function(e) {
            e.preventDefault();
            var button = $(this);
            var resourceID = button.attr('data-id');
            var sync = button.attr('data-sync');

            button.addClass('disabled');

            // Fire off the ajax
            $.ajax({
                url: '/ajax/resources/toggle_sync',
                data: 'resourceID=' + resourceID + '&sync=' + sync,
                success: function() {
                    button.attr('data-sync', (sync == "1") ? 0 : 1);

                    button.find('i').toggleClass('icon-remove').toggleClass('icon-ok');

                    $.ajax({
                        url: '{{ route('collections.createResourceArchive', array($appId, $collection->id)) }}',
                    });
                }
            }).done( function() {
                button.removeClass('disabled');
            });
        });

        $(".toggle-pub").click( function(e) {
            e.preventDefault();
            var button = $(this);
            var resourceID = button.attr('data-id');
            var pub = button.attr('data-pub');

            button.addClass('disabled');

            // Fire off the ajax
            $.ajax({
                url: '/ajax/resources/toggle_pub',
                data: 'resourceID=' + resourceID + '&pub=' + pub,
                success: function() {
                    button.attr('data-pub', (pub == "1") ? 0 : 1);

                    button.find('i').toggleClass('icon-remove').toggleClass('icon-ok');
                }
            }).done( function() {
                button.removeClass('disabled');
            });
        });

        $(document).ready( function() {
            
            $(".icon-exclamation-sign").tooltip();
            
            $(".deleteResourceModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.destroyResource', array($appId, $collectionId, 'id', $language)) }}';

                $("#deleteResourceModal").find('h3').html( "Delete resource <small>" + data_name + "</small>");
                $("#deleteResourceModal").find('.yes').attr('href', url.replace('id', data_id));

                $("#deleteResourceModal").modal('show');
            });
            
            $(".editNameModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.editName', array($appId, $collectionId, 'id', $language)) }}';
                var form = $("#editNameModal").find('form').attr('action');
                
                $("#editNameModal").find('#file-name').html(data_name);
                $("#editNameModal").find('#resource_id').val(data_id);
                $("#editNameModal").find('form').attr('action', form.replace('id', data_id));
                $("#editNameModal input[name=file-name]").attr('value', data_name);

                $("#editNameModal").modal('show');
            });
            
            $(".deleteLocalisationModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.destroy', array($appId, $collectionId, 'id', $language)) }}';

                $("#deleteLocalisationModal").find('h3').html( "Delete localisation of <small>" + data_name + "</small>");
                $("#deleteLocalisationModal").find('#file-name').html(data_name);
                $("#deleteLocalisationModal").find('#language-name').html("{{ Config::get('languages.list')[$language] }}");
                $("#deleteLocalisationModal").find('.yes').attr('href', url.replace('id', data_id));

                $("#deleteLocalisationModal").modal('show');
            });
        
            $(".uploadLocalisationModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.destroy', array($appId, $collectionId, 'id', $language)) }}';
                var form = $("#uploadLocalisationModal").find('form').attr('action');

                $("#uploadLocalisationModal").find('h3').html("Upload a new localisation of <small>" + data_name + "</small>");
                $("#uploadLocalisationModal").find('#file-name').html(data_name);
                $("#uploadLocalisationModal").find('#resource_id').val(data_id);
                $("#uploadLocalisationModal").find('form').attr('action', form.replace('id', data_id));

                $("#uploadLocalisationModal").modal('show');
            });
        });
    </script>

@stop
