@extends('layouts.master')

@section('header')
    @if ($nodeType->exists)
        <h1>Editing Node Type</h1>
    @else
        <h1>New Node Type</h1>
    @endif
@stop

@section('js')
    <script>
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

    $(document).on('click', '.js-enum-add', function(e) {

        e.preventDefault();

        var enum_container = $(this).closest('.control-group').find('.enum_values');

        // Grab the first element, empty it and stick it at the bottom
        var ele = enum_container.find('.js-enum-template').clone();

        var first_ele = enum_container.find('div:first input').attr('name');
        ele.find('input').attr('name', first_ele);

        enum_container.append(ele.html());

    });

    $(document).on('click', '.js-enum-existing-minus', function(e) {

        e.preventDefault();

        if (confirm("Are you sure you want to remove this value? All nodes that have the value set to empty.")) {

            var enum_container = $(this).closest('.input').find('.enum_values');
            if ( enum_container.children().length == 2) {
                alert('You must have at least one value');
            } else {
                $(this).closest('div').remove();
            }

        }

    });

    $(document).on('click', '.js-enum-minus', function(e) {

        e.preventDefault();

        var enum_container = $(this).closest('.input').find('.enum_values');
        if ( enum_container.children().length == 2) {
            alert('You must have at least one value');
        } else {
            $(this).closest('div').remove();
        }

    });

    </script>

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