@extends('layouts.master')

@section('header')
    <h1>Users</h1>
@stop

@section('body')

    <p class="pull-right">
        @if (Sentry::getUser()->hasAccess('cms.users.create'))
            <a href="{{ route('users.create') }}" class="btn"><i class="icon-plus"></i> New User</a>
        @endif
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Created</th>
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
                    @if (Sentry::getUser()->hasAccess('cms.users.update'))
                        <a href="{{ route('users.edit', array($user->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    @endif

                    @if (Sentry::getUser()->hasAccess('cms.users.delete'))
                        <a href="{{ route('users.delete', array($user->id)) }}" data-toggle="modal" class="btn btn-small deleteModal"><i class="icon-trash"></i> Delete</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade hide" id="deleteModal">
        <div style="display: none;" class="userDeleteUrl"></div>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Delete User</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this user? Deleting this user will transfer ownership of all nodes they own to you. This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes" id="deleteModelConfirm">Yes, Delete it.</a>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $('#deleteModelConfirm').on('click', function(e) {
                window.location = $('#deleteModal .userDeleteUrl').html();
            });

            $(".deleteModal").click( function(e) {
                e.preventDefault();
                $('#deleteModal .userDeleteUrl').html($(this).attr('href'));

                $("#deleteModal").modal('show');
            });
        });
    </script>

@stop