@extends('layouts.master')

@section('header')
    <h1>Groups</h1>
@stop

@section('body')

    <p class="pull-right">
        <a href="{{ route('groups.create') }}" class="btn"><i class="icon-plus"></i> New Group</a>
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
                         - {{ implode(', ', $group->users->slice(0, 5)->lists('fullName')) }}&hellip;
                    @endif
                </td>
                <td width="150">
                    <a href="{{ route('groups.edit', array($group->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop