@extends('layouts.master')

@section('header')
    <h1>Resources &raquo; {{ $catalogue->name }}</h1>
@stop

@section('body')

    <div class="pull-right">

        <div class="upload_container">
            <input type="file" multiple="multiple" name="upload" class="file_upload_fallback" id="file_upload_fallback" style="display: block; width: 0px; height: 0px; ">
            <button id="dropzone" class="btn"><i class="icon-upload"></i> Upload a New Resource</button>
        </div>

    </div>

    <div class="progress" id="upload_progress">
      <div class="bar" style="width: 0%;"></div>
    </div>

    <table class="table table-striped resource_table">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Sync</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catalogue->resources as $resource)
            <tr>
                <td>
                    @if ($resource->isImage())
                        <img src="{{ $resource->path() }}?type=view" width="80" height="50" />
                    @else
                        <i class="icon-file"></i>
                    @endif
                </td>
                <td class="resource-name">
                    <a href="{{ $resource->path() }}"
                        @if ($resource->isPdf())
                            data-fancybox-type="iframe"
                        @endif
                        class="fancybox">{{ $resource->displayText }}</a>
                </td>
                <td>
                    @if ($resource->sync)
                        <a href="" class="btn toggle-sync" data-id="{{ $resource->id }}" data-sync="0"><i class="icon-ok"></i></a>
                    @else
                        <a href="" class="btn toggle-sync" data-id="{{ $resource->id }}" data-sync="1"><i class="icon-remove"></i></a>
                    @endif
                </td>
                <td width="150">
                    <a href="#" class="btn btn-small uploadNewVersionModal" style="margin-bottom: 5px;" data-id="{{ $resource->id }}" data-name="{{ $resource->filename }}"><i class="icon-refresh"></i> Upload new Version</a>
                    <a href="#" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" class="btn btn-small deleteModal" data-id="{{ $resource->id }}" data-name="{{ $resource->filename }}"><i class="icon-trash"></i> Delete</a>
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
            <p>Are you sure you want to delete this resource? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes">Yes, Delete it.</a>
        </div>
    </div>

    <div class="modal fade hide" id="uploadNewVersionModal">
        {{ Form::open(['enctype' => 'multipart/form-data', 'url' => route('resources.updateFile', 'id'), 'style' => 'margin-bottom: 0px;']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Upload a new version</h3>
            </div>
            <div class="modal-body">
                <p>Uploading a new verison of <b id="file-name"></b> will overwrite the previous version.</p>

                {{ Form::hidden('resource_id', null, ['id' => 'resource_id']) }}
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

        loadResourceUploader('{{ route('resources.process', array($collection->id, $catalogue->id)) }}', function() {
            document.location.reload(true);
        }, function() {}, [
            @if ($catalogue->restrictions)
                {title : "Allowed Files", extensions : "{{ implode(',', $catalogue->restrictions) }}"},
            @endif
        ], {
            fileSize: '{{ Config::get('core.prefrences.file-upload-limit') }}'
        });

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
                }
            }).done( function() {
                button.removeClass('disabled');
            });
        });

        $(document).ready( function() {
            $(".deleteModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.destroy', 'id') }}';

                $("#deleteModal").find('h3').html( "Delete resource <small>" + data_name + "</small>");
                $("#deleteModal").find('.yes').attr('href', url.replace('id', data_id));

                $("#deleteModal").modal('show');
            });

            $(".uploadNewVersionModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('resources.destroy', 'id') }}';
                var form = $("#uploadNewVersionModal").find('form').attr('action');

                $("#uploadNewVersionModal").find('#file-name').html( data_name );
                $("#uploadNewVersionModal").find('#resource_id').val( data_id );
                $("#uploadNewVersionModal").find('form').attr('action', form.replace('id', data_id));

                $("#uploadNewVersionModal").modal('show');
            });
        });
    </script>

@stop