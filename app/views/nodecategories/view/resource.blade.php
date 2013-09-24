<?php

    if (@$data->{$column->name}) {
        $resource = Resource::find($data->{$column->name});
    } else {
        $resource = null;
    }

?>

@if ($resource)
    @if ($resource->isImage())
        <a href="{{ $resource->path() }}" class="fancybox" title="{{ $resource->caption }}">
            <img src="{{ $resource->path() }}?type=view" width="100" />
        </a>
    @else
        <a href="{{ $resource->path() }}" class="fancybox" title="{{ $resource->caption }}">
            <i class="icon-file" style="font-size: 100px"></i>
        </a>
    @endif
@else
    <p>-</p>
@endif

<script>
    $(document).ready( function() {
        $(".fancybox").fancybox({
            padding: 0,
            iframe : {
                preload: false
            }
        });
    })
</script>