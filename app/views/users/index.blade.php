@extends('layouts.master')

@section('header')
    <h1>Users</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('users.create'))
            <a href="{{ route('users.create') }}" class="btn"><i class="icon-plus"></i> New User</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    {{ $user->first_name }}
                </td>
                <td>
                    {{ $user->last_name }}
                </td>
                <td>
                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                </td>
                <td>
                    @if (is_null($user->last_login))
                        Never
                    @else
                        {{ date('j-m-Y', strtotime($user->last_login)) }}
                    @endif
                </td>
                <td width="150">
                    <a href="{{ route('users.edit', array($user->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop