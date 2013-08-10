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
                        <i class="icon-ok"></i>
                    @else
                        <i class="icon-remove"></i>
                    @endif
                </td>
                <td width="150">
                    <a href="#" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <button class="btn btn-small"><i class="icon-trash"></i> Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

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

        loadResourceUploader('{{ route('resources.process', array($catalogue->id)) }}', function() {
            document.location.reload(true);
        }, function() {}, [
            @if ($catalogue->restrictions)
                {title : "Allowed Files", extensions : "{{ implode(',', $catalogue->restrictions) }}"},
            @endif
        ]);

    </script>

@stop