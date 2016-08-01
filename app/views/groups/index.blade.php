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
                <th>Hierarchy level</th>
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
                <td>{{ $group->hierarchy }}</td>
                <td width="150">
                    @if (Sentry::getUser()->hasAccess('cms.groups.update'))
                        <a href="{{ route('groups.edit', array($group->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    @endif
                    @if (Sentry::getUser()->hasAccess('cms.groups.delete'))
                        <a href="{{ route('groups.delete', array($group->id)) }}" class="btn btn-small deleteModal"><i class="icon-trash"></i> Delete</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade hide" id="deleteModal">
        <div style="display: none;" class="groupDeleteUrl"></div>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3></h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this group? Any users in this group will loose their permissions. This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes" id="deleteModelConfirm">Yes, Delete it.</a>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $('#deleteModelConfirm').on('click', function(e) {
                window.location = $('#deleteModal .groupDeleteUrl').html();
            });

            $(".deleteModal").click( function(e) {
                e.preventDefault();
                $('#deleteModal .groupDeleteUrl').html($(this).attr('href'));

                $("#deleteModal").modal('show');
            });
        });
    </script>

@stop