@extends('layouts.master')

@section('header')
    <h1>Catalogues</h1>
@stop

@section('body')

    <p class="pull-right">
        <a href="{{ route('catalogues.create') }}" class="btn"><i class="icon-plus"></i> New Catalogue</a>
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Restrictions</th>
                <th>Collections</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catalogues as $catalogue)
            <tr>
                <td>
                    {{ $catalogue->name }}
                </td>
                <td>
                    {{ implode(', ', $catalogue->restrictions) }}
                </td>
                <td>
                    {{ implode('<br />', $catalogue->collections->lists('name')) }}
                </td>
                <td width="150">
                    <a href="{{ route('catalogues.edit', array($catalogue->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                    <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop