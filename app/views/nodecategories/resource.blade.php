<?php

    if ($data) {
        $resource = Resource::find(@$data->{$column->name});
    } else {
        $resource = null;
    }

?>
{{--
<div class="resource_container">
    {{ Form::hidden('nodetype[' . $column->name . ']', Input::old('nodetype.' . $column->name , @$data->{$column->name}), array('id' => 'input_' . $column->name)) }}
    <p><em>{{ @$column->description }}</em></p>

    <div class="resource_form">

        @if ($resource)
            <div class="resource_preview" style="text-align: center;">
                @if ($resource->isImage())
                    <a href="{{ $resource->path() }}" class="fancybox" title="{{ $resource->caption }}">
                        <img src="{{ $resource->path() }}?type=thumb" height="50" />
                    </a>
                @else
                    <a href="{{ $resource->path() }}" class="fancybox" title="{{ $resource->caption }}">
                        <i class="icon-file" style="font-size: 100px"></i>
                    </a>
                @endif
            </div>
            <p class="resource_filename">{{ $resource->filename }}</p>
            <p class="resource_caption">{{ $resource->caption }}</p>
            <p><a href="#" class="resource_remove">Remove</a> <a href="#resource_list" data-filter="{{ $column->show }}" data-dest="resource" data-resource="input_{{ $column->name }}" class="resource_fancybox">Choose One</a></p>
        @else
            <div class="resource_preview" style="text-align: center;"></div>
            <p class="resource_filename">No Resource Selected</p>
            <p class="resource_caption"></p>
            <p><a href="#" style="display: none" class="resource_remove">Remove</a> <a href="#resource_list" data-filter="{{ $column->show }}" data-dest="resource" data-resource="input_{{ $column->name }}" class="resource_fancybox">Choose One</a></p>
        @endif

    </div>
</div>
--}}