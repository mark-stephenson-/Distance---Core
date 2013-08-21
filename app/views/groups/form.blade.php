@extends('layouts.master')

@section('header')
    @if ($group->exists)
        <h1>Editing Group</h1>
    @else
        <h1>New Group</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($group, 'groups', null, false) }}

    <div class="control-group">
        {{ Form::label('name', 'Name', array('class' => 'control-label')) }}
        <div class="controls">
            {{ Form::text('name', null, array('class' => 'span11')) }}
        </div>
    </div>

    <div class="control-group">
        {{ Form::label('members', 'Members', array('class' => 'control-label')) }}
        <div class="controls">
            <?php
                // Laravel isn't working as expected, so here's a bug fix for User::all()->lists('full_name', 'id')
                $user_list = array();

                foreach ( User::all() as $_user ) {
                    $user_list[$_user->id] = $_user->full_name;
                }
            ?>

            {{ Form::select('members[]', $user_list, $group->users->lists('id'), array('class' => 'span11 select2', 'multiple' => 'multiple', 'data-placeholder' => 'Select the group members')) }}
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