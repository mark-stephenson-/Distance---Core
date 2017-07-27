@extends('layouts.master')

@section('header')
    <h1>Data Export</h1>
@stop

@section('body')
    <p class="lead">Your file is ready to download</p>

    <p><a href="{{ route('data.export.download') }}">Download Export</a></p>
@stop