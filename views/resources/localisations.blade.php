@extends('layouts.master')

@section('header')
    <h1>Localisations &#8227; {{ $resource->filename }}</h1>
@stop

<?php $language = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1); ?>

@section('body')
    <div class="btn-group pull-left">
        <a href="{{ route('resources.show', array($appId, $collectionId, $catalogue->id, $language)) }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
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
                <th>Language</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resource->localisations as $localisation)
            <tr>
                <td style="width: 80px !important; padding-right: 30px; height: 50px  !important; overflow: hidden;">
                    @if ($resource->isImage($localisation->lang))
                        <a href="{{ $resource->path($localisation->lang) }}" @if ($resource->isPdf($localisation->lang)) data-fancybox-type="iframe" @endif class="fancybox">
                            <img style="max-width: 80px;" src="{{ $resource->path($localisation->lang) }}?type=view" width="80" height="50" />
                        </a>
                    @else
                        <i class="icon-file"></i>
                    @endif
                </td>
                <td>
                    <a href="{{ route('resources.localisations', array($appId, $collectionId, $resource->catalogue_id, $resource->id, $localisation->lang)) }}">
                        {{ Config::get("languages.list")[$localisation->lang] }}
                    </a>
                </td>
                <td>
                    <a href="javascript:void(0)" class="btn btn-small replaceLocalisationModal" data-id="{{ $resource->id }}" data-lang="{{ $localisation->lang }}" data-name="{{ $resource->filename }}">
                        <i class="icon-refresh"></i> Replace Localisation
                    </a>
                    <a href="javascript:void(0)" class="btn btn-small deleteLocalisationModal" data-id="{{ $resource->id }}" data-lang="{{ $localisation->lang }}" data-name="{{ $resource->filename }}">
                        <i class="icon-trash"></i> Delete Localisation
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

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

    <div class="modal fade hide" id="replaceLocalisationModal">
        {{ Form::open(array('enctype' => 'multipart/form-data', 'url' => route('resources.updateFile', array($appId, $collection->id, 'id', 'lang', true)), 'style' => 'margin-bottom: 0px;')) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3></h3>
            </div>
            <div class="modal-body">
                <p>Uploading a new <b><u id="language-name"></u></b> version of <b id="file-name"></b> will overwrite the previous version.</p>
                {{ Form::hidden('resource_id', null, array('id' => 'resource_id')) }}
                {{ Form::hidden('resource_lang', null, array('lang' => 'resource_lang')) }}
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

        loadResourceUploader('{{ route('resources.updateFile', array($appId, $collection->id, $resource->id, $language)) }}', function() {
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

        $("select[name=language]").change(function() {
            var href = "{{ route('resources.localisations', array($appId, $collectionId, $catalogue->id, $resource->id, 'en')) }}";
            window.location.href = href.substring(0, href.lastIndexOf('/') + 1) + $(this).val();
        });
        
        $(document).ready( function() {
            
            $(".deleteLocalisationModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_lang = $(this).attr('data-lang');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.destroy', array($appId, $collectionId, 'id', 'lang', true)) }}';

                $("#deleteLocalisationModal").find('h3').html( "Delete localisation of <small>" + data_name + "</small>");
                $("#deleteLocalisationModal").find('#file-name').html(data_name);
                $("#deleteLocalisationModal").find('#language-name').html({{ json_encode(Config::get('languages.list')) }}[data_lang]);
                $("#deleteLocalisationModal").find('.yes').attr('href', url.replace('id', data_id).replace('lang', data_lang));

                $("#deleteLocalisationModal").modal('show');
            });
        
            $(".replaceLocalisationModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_lang = $(this).attr('data-lang');
                var data_name = $(this).attr('data-name');
                var form = $("#replaceLocalisationModal").find('form').attr('action');

                $("#replaceLocalisationModal").find('h3').html("Upload a new localisation of <small>" + data_name + "</small>");
                $("#replaceLocalisationModal").find('#file-name').html(data_name);
                $("#replaceLocalisationModal").find('#resource_id').val(data_id);
                $("#replaceLocalisationModal").find('#resource_lang').val(data_lang);
                $("#replaceLocalisationModal").find('form').attr('action', form.replace('id', data_id).replace('lang', data_lang));

                $("#replaceLocalisationModal").modal('show');
            });
        });
    </script>

@stop
