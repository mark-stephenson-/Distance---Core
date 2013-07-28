@extends('layouts.master')

@section('header')
    @if ($group->exists)
        <h1>Editing Group</h1>
    @else
        <h1>New Group</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($group, 'groups') }}

    <div class="control-group">
        {{ Form::label('name', 'Name', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::text('name', null, ['class' => 'span11']) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('members', 'Members', ['class' => 'control-label']) }}
        <div class="controls">
            {{ Form::select('members[]', User::all()->lists('fullName', 'id'), $group->users->lists('id'), ['class' => 'span11 select2', 'multiple' => 'multiple', 'data-placeholder' => 'Select the group members']) }}
        </div>
    </div>

    <section class="permission-tree">
        {{ $permissions }}
    </section>

    <div class="form-actions">
        @if ($group->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create Group" />
        @endif
    </div>

    {{ Form::close() }}

@stop