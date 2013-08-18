@extends('layouts.master')

@section('header')
    <h1>Groups</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('cms.groups.create'))
            <a href="{{ route('groups.create') }}" class="btn"><i class="icon-plus"></i> New Group</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
            <tr>
                <td>
                    {{ $group->name }}
                </td>
                <td>
                    {{ $userCount = count($group->users) }}

                    @if ($userCount)
                    <?php
                        // Laravel isn't working as expected, so here's a bug fix for User::all()->lists('full_name', 'id')
                        $user_list = array();

                        foreach ( $group->users->slice(0, 5) as $_user ) {
                            $user_list[$_user->id] = $_user->full_name;
                        }
                    ?>
                         - {{ implode(', ', $user_list) }}&hellip;
                    @endif
                </td>
                <td width="150">
                    @if (Sentry::getUser()->hasAccess('cms.groups.update'))
                        <a href="{{ route('groups.edit', array($group->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    @endif
                    @if (Sentry::getUser()->hasAccess('cms.groups.delete'))
                        <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop