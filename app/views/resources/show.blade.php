@extends('layouts.master')

@section('header')
    <h1>Resources &raquo; {{ $catalogue->name }}</h1>
@stop

@section('body')

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
                        <img src="{{ $resource->path() }}" width="80" height="50" />
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

    </script>

@stop