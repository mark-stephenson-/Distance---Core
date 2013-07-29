@extends('layouts.master')

@section('header')
    <h1>Resources</h1>
@stop

@section('body')

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Filetypes</th>
                <th>Files</th>
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
                    @if (count($catalogue->restrictions))
                        <span class="label">{{ implode('</span> <span class="label">', $catalogue->restrictions) }}</span>
                    @else
                        No Restrictions
                    @endif
                </td>
                <td>
                    {{ $catalogue->resources()->count() }}
                </td>
                <td width="150">
                    <a href="{{ route('resources.show', array($catalogue->id)) }}" class="btn btn-small"><i class="icon-search"></i> View Resources</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop