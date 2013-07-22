@extends('layouts.master')

@section('header')
    @if ($nodeType->exists)
        <h1>Editing Node Type</h1>
    @else
        <h1>New Node Type</h1>
    @endif
@stop

@section('js')
    
    $('#collections, #js-category-select').select2();

    $("#js-nodes_container").sortable({
        handle: '.drag_handle',
        placeholder: 'sortable_placeholder',
        forcePlaceholderSize: true
    });

    $('#js-category-add').on('click', function(e) {

        e.preventDefault();

        var chosen_category = $('#js-category-select option:selected').val();

        $.ajax({
            url: "{{ route('node-types.form-template') }}",
            method: 'POST',
            data: {
                category: chosen_category
            },
            success: function(data) {
                var template = $('#js-category_template');
                var injected_template = template.find('li').html(data);
                $('#js-nodes_container').append(template.html());
            },
            error: function(xhr, status, errorString) {
                alert(errorString)
            }
        });

    });

    $(document).on('click', '.js-remove-category', function(e) {
        e.preventDefault();
        $(this).closest('li').remove();
    });

@stop

@section('body')
    
    {{ formModel($nodeType, 'node-types') }}

    <div class="control-group">
        {{ Form::label('label', 'Name', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('label', null, ['class' => 'span8']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('collections', 'Collections', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::select('collections[]', Collection::toDropDown(), $nodeCollections, ['class' => 'span8', 'multiple' => 'multiple', 'id' => 'collections']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('category', 'Category', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::select('category', NodeType::categorySelect(), null, ['id' => 'js-category-select', 'class' => 'span7']) }}
            <a href="#" id="js-category-add" class="btn" style="margin-left: 10px">Add</a>
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            @if (!$nodeType->exists)
                {{ Form::submit('Create Collection', ['class' => 'btn']) }}
            @else
                {{ Form::submit('Save Changes', ['class' => 'btn']) }}
            @endif
        </div>
    </div>

    <ul id="js-nodes_container">
        @if ($nodeType->exists)
            @foreach($nodeType->columns as $column)
                <li class="well">
                    {{ NodeType::viewForCategory($column->category, $column) }}
                </li>
            @endforeach
        @endif
    </ul>

    @if ($nodeType->exists)
    <div class="control-group">
        <div class="controls">
            {{ Form::submit('Save Changes', ['class' => 'btn']) }}
        </div>
    </div>
    @endif

    {{ Form::close() }}

    <div id="js-category_template" style="display: none"><li class="well"></li></div>
@stop