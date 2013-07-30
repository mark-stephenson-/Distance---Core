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
            {{ Form::text('first_name', null, ['class' => 'span11']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('last_name', 'Last Name', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('last_name', null, ['class' => 'span11']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('email', 'Email', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('email', null, ['class' => 'span11']) }}
        </div>
    </div>

     <hr />

    <div class="control-group">
        {{ Form::label('bio', 'Bio', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::textarea('bio', null, ['class' => 'span11']) }}
        </div>
    </div>

    @if ($field_1 = Config::get('core.labels.user_field_1'))
        <div class="control-group">
            {{ Form::label('field_1', $field_1, ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::text('field_1', Input::old('field_1', $user->field_1), ['class' => 'span11']) }}
            </div>
        </div>
    @endif

    @if ($field_2 = Config::get('core.labels.user_field_2'))
        <div class="control-group">
            {{ Form::label('field_2', $field_2, ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::text('field_2', Input::old('field_2', $user->field_2), ['class' => 'span11']) }}
            </div>
        </div>
    @endif

    @if ($field_3 = Config::get('core.labels.user_field_3'))
        <div class="control-group">
            {{ Form::label('field_3', $field_3, ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::text('field_3', Input::old('field_3', $user->field_3), ['class' => 'span11']) }}
            </div>
        </div>
    @endif

    @if ($field_4 = Config::get('core.labels.user_field_4'))
        <div class="control-group">
            {{ Form::label('field_4', $field_4, ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::text('field_4', Input::old('field_4', $user->field_4), ['class' => 'span11']) }}
            </div>
        </div>
    @endif

    @if ($field_5 = Config::get('core.labels.user_field_5'))
        <div class="control-group">
            {{ Form::label('field_5', $field_5, ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::text('field_5', Input::old('field_5', $user->field_5), ['class' => 'span11']) }}
            </div>
        </div>
    @endif

    <hr />

    <div class="alert">
        Only enter a password if you want to change it.
    </div>

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

    <hr />
    
    <div class="alert alert-error">
        Please enter your current password to confirm who you are.
    </div>
    <div class="control-group">
        {{ Form::label('current_password', 'Current Password', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::password('current_password') }}
        </div>
    </div>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Save changes" />
    </div>

    {{ Form::close() }}
@stop