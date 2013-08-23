@extends('layouts.master')

@section('header')
    @if ($collection->exists)
        <h1>Editing Collection</h1>
    @else
        <h1>New Collection</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($collection, 'collections') }}

    <div class="control-group">
        {{ Form::label('name', 'Name', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('name', null, array('class' => 'span8')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('api_key', 'API Key', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('api_key', Input::old('api_key', ($collection->api_key) ?: md5(rand())), array('class' => 'span8')) }}
        </div>
    </div>

    @if ( $collection->exists )
        <?php
            if ( $collection->logo_id ) {
                $logo = Resource::find($collection->logo_id);
            } else {
                $logo = null;
            }
        ?>
        <div class="control-group">
            {{ Form::label('image', 'Image', array('class' => 'control-label')) }}

            <div class="controls">
                <div class="resource-container  resource-view">
                    {{ Form::hidden('logo_id', @$logo->id, array('id' => 'logo_id')) }}

                    @if ( $logo )
                        <div class="resource">
                            <div class="image">
                                @if ( $logo->isImage() )
                                    <img src="/file/{{ $logo->filename }}?type=view" alt="" />
                                @else
                                    <i class="icon-file"></i>
                                @endif
                            </div>

                            <p class="filename">{{ $logo->filename }}</p>
                            <a href="#resource_window" data-toggle="modal">Change</a>
                        </div>
                    @else
                        <div class="resource">
                            <p style="padding-top: 5px;">No Resource Selected.</p>
                            <a href="#resource_window" data-toggle="modal">Choose One</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="control-group">
        <div class="controls">
            @if (!$collection->exists)
                {{ Form::submit('Create Collection', array('class' => 'btn')) }}
            @else
                {{ Form::submit('Save Changes', array('class' => 'btn')) }}
            @endif
        </div>
    </div>

    {{ Form::close() }}

    @if ( $collection->exists )
    <div class="modal hide fade" id="resource_window">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Resources</h3>
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
                    <?php
                        $catalogues = $collection->catalogues;
                        if (count($catalogues)) {
                            $resources = Resource::whereIn('catalogue_id', $catalogues->lists('id'))->get();
                        } else {
                            $resources = array();
                        }
                    ?>
                    @foreach( $resources as $resource )
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

    <div class="empty-resource" style="display: none">
        <div class="resource">
            <div class="image"></div>

            <p class="filename"></p>
            <a href="#resource_window" data-toggle="modal">Change</a>
        </div>
    </div>
    @endif

<script>
    $("#resource_window").on('shown', function() {
        $("#resource_window a").click( function(e) {
            e.preventDefault();

            $("#logo_id").val( $(this).attr('data-id') );
            $(".resource-container .resource").remove();
            $(".resource-container").prepend( $('.empty-resource').html() );

            $(".resource-container .resource .filename").html( $(this).attr('data-filename') );

            if ( $(this).attr('data-image') == "true") {
                $(".resource-container .resource .image").html( '<img src="/file/' + $(this).attr('data-filename') +'?type=view" />' );
            }
            $("#resource_window").modal('hide');
        });
    });
</script>
@stop