@extends('layouts.master')

@section('header')
    @if ($node->exists)
        <h1>Editing Node</h1>
    @else
        <h1>New Node</h1>
    @endif
@stop

@section('js')

@stop

@section('body')

    <div class="btn-group pull-left">
        @if ( $lastView = Session::get('last-view') )
            @if ( $lastView['collection_id'] == $collection->id)
                <a href="{{ $lastView['url'] }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
            @else
                <a href="{{ route('nodes.list', array($collection->application_id, $collection->id)) }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
            @endif
        @else
            <a href="{{ route('nodes.list', array($collection->application_id, $collection->id)) }}" class="btn"><i class="icon-arrow-left"></i> Back</a>
        @endif
    </div>

    <div style="clear: both; padding-top: 15px;"></div>
    
    @if ($node->exists)
        {{ Form::open(array('route' => array('nodes.update', $collection->application_id, $collection->id, $node->id, $revisionData->id, $branchId), 'class' => 'form-horizontal')) }}
    @else
        {{ Form::open(array('route' => array('nodes.store', $collection->application_id, $collection->id, $nodeType->id, $parentId), 'class' => 'form-horizontal')) }}
    @endif
    
    <div class="control-group">
        {{ Form::label('title', 'Title', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('title', Input::old('title', $node->title), array('class' => 'span8')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('owned_by', 'Owner', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::select('owned_by', $node->potentialOwners(), Input::old('owned_by', $node->owned_by), array('class' => 'span8 select2', 'id' => 'js-owner-select')) }}
            
            <?php $categories = array_fetch($nodeType->columns, 'category') ?>
            
            @if ((in_array('string-i18n', $categories) || in_array('html-i18n', $categories) || in_array('resource-i18n', $categories)) && count($categories) > 1)
                <div class="pull-right">
                    <i class="icon-globe" data-toggle="tooltip" title="Toggle the localisation of all internationalised categories."></i>
                    {{ Form::select("language", array("" => "") + Config::get("languages.list"), 'en', array("class" => "master-select", "style" => "margin:0 0 4px 4px")) }}
                </div>
            @endif
        </div>
    </div>
    
    <div class="well">
        @foreach($nodeType->columns as $column)
            @if (Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $nodeType->name . '.columns.' . $column->name))
                <div class="control-group">
                    {{ Form::label($column->name, $column->label, array('class' => 'control-label')) }}
                    <div class="controls">
                        @include('nodecategories.' . $column->category, array('column' => $column, 'node' => $node, 'data' => @$revisionData))
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="form-actions">
        @if ($node->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Node" />
        @endif
    </div>

    <script>
        $(function(){
            $(".icon-globe").tooltip();
            $(".control-group select[name=language] option:first-child").attr("disabled", true);
            
            $("select[name=language].master-select").change(function(){
                $("select[name=language].child-select").val($(this).val());
                $("select[name=language].child-select").change();
            });
            
            var lang = $("select[name=language].child-select").val();
            
            $("select[name=language].child-select").change(function(){
                $("select[name=language].child-select").each(function(){
                    if ($(this).val() != $("select[name=language].child-select").val()) {
                        $("select[name=language].master-select").val("");
                        return false;
                    } else {
                        $("select[name=language].master-select").val($(this).val());  
                    }
                });
            });
        });
    </script>

@stop