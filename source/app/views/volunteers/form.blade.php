@extends('layouts.master')

<?php

    if ($volunteer->exists and $trustId = $volunteer->latestRevision()->trust) {
        $trust = Node::whereNodeTypeIs(2, 'published')->find($trustId);
    }

    if ($volunteer->exists) {
        $volunteerData = $volunteer->latestRevision();
    } else {
        $volunteerData = new \stdClass();
        $volunteerData->username = null;
        $volunteerData->password = null;
        $volunteerData->firstname = null;
        $volunteerData->lastname = null;
        $volunteerData->trust = null;
    }
?>

@section('header')
    @if ($volunteer->exists)
        <h1>Editing Volunteer</h1>
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
                {{ Form::label('trust', 'Trust', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::hidden('trust', null, array('id' => 'input_trust')) }}
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


    var trust_preload_data = [];
@if ($volunteerData->trust)
    trust_preload_data.push({ 'id': {{ $volunteer->latestRevision()->trust }}, 'text': "{{ $trust->name }}" });
@endif

    $(document).ready(function() {

        $('#input_trust').select2({

            placeholder: "Start Typing To Search",
            minimumInputLength: 2,
            maximumSelectionSize: 1,
            multiple:true,
            ajax: {
                url: '{{ route('nodes.lookup', array(CORE_APP_ID, CORE_COLLECTION_ID)) }}?type=2',
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

        $('#input_trust').select2('data', trust_preload_data);

    });
</script>

@stop
