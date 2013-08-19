@extends('layouts.master')

@section('header')
    @if ($user->exists)
        <h1>Editing User</h1>
    @else
        <h1>New User</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($user, 'users', array('autocomplete' => 'off')) }}

    <ul class="nav nav-tabs">
        <li class="active"><a href="#info" data-toggle="tab">User Info</a></li>

        @if ( $user->exists )
            <li><a href="#groups" data-toggle="tab">Groups</a></li>
        @endif
        <!-- <li><a href="#permissions" data-toggle="tab">Permissions</a></li> -->
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            <div class="control-group">
                {{ Form::label('first_name', 'First Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('first_name', null, array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('last_name', 'Last Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('last_name', null, array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('email', 'Email Address', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('email', null, array('class' => 'span11')) }}
                </div>
            </div>

            <hr />

            <div class="control-group">
                {{ Form::label('bio', 'Bio', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::textarea('bio', null, array('class' => 'span11')) }}
                </div>
            </div>

            @if ($field_1 = Config::get('core.labels.user_field_1'))
                <div class="control-group">
                    {{ Form::label('field_1', $field_1, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_1', Input::old('field_1', $user->field_1), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_2 = Config::get('core.labels.user_field_2'))
                <div class="control-group">
                    {{ Form::label('field_2', $field_2, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_2', Input::old('field_2', $user->field_2), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_3 = Config::get('core.labels.user_field_3'))
                <div class="control-group">
                    {{ Form::label('field_3', $field_3, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_3', Input::old('field_3', $user->field_3), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_4 = Config::get('core.labels.user_field_4'))
                <div class="control-group">
                    {{ Form::label('field_4', $field_4, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_4', Input::old('field_4', $user->field_4), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_5 = Config::get('core.labels.user_field_5'))
                <div class="control-group">
                    {{ Form::label('field_5', $field_5, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_5', Input::old('field_5', $user->field_5), array('class' => 'span11')) }}
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
                {{ Form::label('password', 'Password', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::password('password', null, array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('password_confirmation', 'Password Confirmation', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::password('password_confirmation', null, array('class' => 'span11')) }}
                </div>
            </div> 

        </div>

        @if ( $user->exists )
        <div class="tab-pane" id="groups">
            <?php
                $currentGroups = $user->getGroups()->lists('id', 'name');
            ?>

            <div class="row">
                <ul class="span8 offset2 groups">
                    @foreach ( $groups as $group )
                        <li>
                            <div class="heading">
                                <p class="lead pull-left">{{ $group->name }}</p>
                                <div class="pull-right">
                                    @if ( in_array($group->id, $currentGroups) )
                                        @if ( Sentry::getUser()->hasAccess('admin.users.users.removegroup') )
                                            <a href="{{ route('users.remove-group', array($user->id, $group->id)) }}" class="btn btn-danger">Remove from Group</a>
                                        @endif
                                    @else
                                        @if ( Sentry::getUser()->hasAccess('admin.users.users.addgroup') )
                                            <a href="{{ route('users.add-group', array($user->id, $group->id)) }}" class="btn btn-success">Add to Group</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- /#permissions.tab-pane -->
        @endif

        <!-- <div class="tab-pane" id="permissions">
            @if ($user->exists)
                <section class="permission-tree">
                    {{ $permissions }}
                </section>
            @else
                <p>Please create the user before trying to add permission overrides</p>
            @endif
        </div> -->
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