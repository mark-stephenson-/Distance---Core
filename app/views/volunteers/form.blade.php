@extends('layouts.master')

<?php
    if ($volunteer->exists and $wardId = $volunteer->latestRevision()->ward) {
        $ward = Node::find($wardId);
    }

    if ($volunteer->exists) {
        $volunteerData = $volunteer->latestRevision();
    } else {
        $volunteerData = new \stdClass;
        $volunteerData->username = null;
        $volunteerData->password = null;
        $volunteerData->firstname = null;
        $volunteerData->lastname = null;
        $volunteerData->ward = null;
    }
?>

@section('header')
    @if ($volunteer->exists)
        <h1>Editing User</h1>
    @else
        <h1>New User</h1>
    @endif
@stop

@section('body')
    
    {{ Form::open(array('autocomplete' => 'off')) }}

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            @if (!$volunteer->exists)
                <div class="control-group">
                    {{ Form::label('username', 'Username', array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('username', Input::old('username', $volunteerData->username), array('class' => 'span11')) }}
                    </div>
                </div>            
            @else
                {{ Form::hidden('username', $volunteerData->username) }}
            @endif

            <div class="control-group">
                {{ Form::label('password', 'Password', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('password', Input::old('password', $volunteerData->password), array('class' => 'span11')) }}
                </div>
            </div>

            <hr />

            <div class="control-group">
                {{ Form::label('firstname', 'First Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('firstname', Input::old('firstname', $volunteerData->firstname), array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('lastname', 'Last Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('lastname', Input::old('lastname', $volunteerData->lastname), array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('ward', 'Ward', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::hidden('ward', null, array('id' => 'input_ward')) }}
                </div>
            </div>
            

        </div>

    </div>   

    <div class="form-actions">
        @if ($volunteer->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create User" />
        @endif
    </div>

    {{ Form::close() }}

    <script>

var ward_preload_data = [];
@if ($volunteerData->ward)
    ward_preload_data.push({ 'id': {{ $volunteer->latestRevision()->ward }}, 'text': "{{ $ward->title }}" });
@endif

    $(document).ready(function() {

        $('#input_ward').select2({

            placeholder: "Start Typing To Search",
            minimumInputLength: 2,
            maximumSelectionSize: 1,
            multiple:true,
            ajax: {
                url: '{{ route('nodes.lookup', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}?type=4',
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term
                    }
                },
                results: function (data, page) {
                    return data;
                }
            }
        });

        $('#input_ward').select2('data', ward_preload_data);

    });
</script>

@stop