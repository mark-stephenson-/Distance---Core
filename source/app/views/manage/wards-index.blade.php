@extends('layouts.master')

@section('header')
    <h1>Wards</h1>
@stop

@section('body')
    <h2>{{ $hospital->latestRevision()->name }}</h2>

    <p class="pull-right">
        <a href="{{ route('manage.trust.index', array($trust->id)) }}" class="btn"><i class="icon-arrow-left"></i> Hospitals</a>
        <a href="{{ route('manage.ward.create', array($trust->id, $hospital->id)) }}" class="btn"><i class="icon-plus"></i> New Ward</a>
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wards as $ward)
                <?php
                    $wardData = $ward->latestRevision();
                ?>
            <tr>
                <td>
                    {{ $wardData->name }}
                </td>
                <td width="450">
                    <a href="{{ route('manage.ward.edit', array($trust->id, $hospital->id, $ward->id)) }}" class="btn btn-small"><i class="icon-pencil"></i> Edit</a>
                    <a href="{{ route('manage.ward.merge', array($trust->id, $hospital->id, $ward->id)) }}" class="btn btn-small"><i class="icon-code-fork"></i> Merge into Another</a>
                    <a href="{{ route('manage.ward.delete', array($trust->id, $hospital->id, $ward->id)) }}" data-toggle="modal" class="btn btn-small deleteModal" data-toggle="modal"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade hide" id="deleteModal">
        <div style="display: none;" class="userDeleteUrl"></div>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Close Ward</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to close this ward?</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary yes" id="deleteModelConfirm">Yes, Close it.</a>
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