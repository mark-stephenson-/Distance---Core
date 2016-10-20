@extends('layouts.master')

<?php
    $trustIds = array();

    foreach ($volunteers as $volunteer) {
        if ($trustId = $volunteer->latestRevision()->trust) {
            $trustIds[] = $volunteer->latestRevision()->trust;
        }
    }

    if (count($trustIds)) {
        $trusts = Node::whereNodeTypeIs(2, 'published')->whereIn('nodes.id', $trustIds)->lists('name', 'id');
    }

    $trusts[0] = $trusts[''] = 'No Trust';
?>

@section('header')
    <h1>Volunteers</h1>
@stop

@section('body')
    @if(Sentry::getUser()->hasAccess('cms.volunteers.manage.*'))
        <p class="pull-right">
            <a href="{{ route('volunteers.create') }}" class="btn"><i class="icon-plus"></i> New Volunteer</a>
        </p>
    @endif
    <table class="table table-striped" id="volunteer-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Trust</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($volunteers as $volunteer)
                <?php
                    $volunteerData = $volunteer->latestRevision();
                ?>
            <tr>
                <td>
                    {{ $volunteerData->username }}
                </td>
                <td>
                    {{ $volunteerData->firstname }}
                </td>
                <td>
                    {{ $volunteerData->lastname }}
                </td>
                <td>
                    {{ $trusts[$volunteerData->trust] }}
                </td>
                <td width="150">
                    @if(Sentry::getUser()->hasAccess('cms.volunteers.manage.*'))
                        <a href="{{ route('volunteers.edit', array($volunteer->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                        <a href="{{ route('volunteers.delete', array($volunteer->id)) }}" data-toggle="modal" class="btn btn-small deleteModal"><i class="icon-trash"></i> Delete</a>
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

@stop

@section('js')
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jqc-1.12.3/dt-1.10.12/datatables.min.js"></script>

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
        $('#volunteer-table').dataTable({
            pageLength: 25,
            oSearch: {
                sSearch: "{{ Input::get('search') }}"
            }
        });

    </script>
@stop