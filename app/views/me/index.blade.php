@extends('layouts.master')

@section('header')
    <h1>Editing Your Profile</h1>
@stop

@section('body')
    <h2>Profile Information</h2>

    {{ Form::model($user, ['class' => 'form-horizontal', 'autocomplete' => 'off']) }}

    <div class="control-group">
        {{ Form::label('first_name', 'First Name', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('first_name', null) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('last_name', 'Last Name', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('last_name', null) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('email', 'Email', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('email', null) }}
        </div>
    </div>

    <h2>Update Password</h2>

    <div class="control-group">
        {{ Form::label('password', 'New Password', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::password('password') }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('password_confirmation', 'Confirm New Password', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::password('password_confirmation') }}
        </div>
    </div>

    <h2 style="margin-top: 75px;">Please enter your current password, to confirm who you are.</h2>
        <div class="control-group">
        {{ Form::label('current_password', 'Current Password', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::password('current_password') }}
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            {{ Form::submit('Save Changes', ['class' => 'btn']) }}
        </div>
    </div>

    {{ Form::close() }}
@stop