<?php

    if (@$data->{$column->name}) {
        $resource = Resource::find($data->{$column->name});
    } else {
        $resource = null;
    }

?>

@if ($resource)
    <ul>
        @foreach($resource->localisations as $localisation)
            <li>
                <span>[{{ strtoupper($localisation->lang) }}]</span>
                @if ($resource->isImage($localisation->lang))
                    <a href="{{ $resource->path($localisation->lang) }}" class="fancybox" title="{{ $resource->caption }}">
                        <img src="{{ $resource->path($localisation->lang) }}?type=view" width="100" style="margin: 10px 0;"/>
                    </a>
                @else
                    <a href="{{ $resource->path($localisation->lang) }}" class="fancybox" title="{{ $resource->caption }}">
                        <i class="icon-file" style="font-size: 100px"></i>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
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