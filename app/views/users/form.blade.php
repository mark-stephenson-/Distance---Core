@extends('layouts.master')

@section('header')
    @if ($user->exists)
        <h1>Editing User</h1>
    @else
        <h1>New User</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($user, 'users', ['autocomplete' => 'off']) }}

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
        {{ Form::label('email', 'Email Address', ['class' => 'control-label']) }}
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

    @if ($user->exists)
        <div class="alert">
            Only enter a password if you want to change it.
        </div>
    @endif

    <div class="control-group">
        {{ Form::label('password', 'Password', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::password('password', null, ['class' => 'span11']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('password_confirmation', 'Password Confirmation', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::password('password_confirmation', null, ['class' => 'span11']) }}
        </div>
    </div>    

    <div class="form-actions">
        @if ($user->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create User" />
        @endif
    </div>

    {{ Form::close() }}

@stop