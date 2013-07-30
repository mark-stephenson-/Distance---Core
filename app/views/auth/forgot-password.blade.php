@extends('layouts.auth')

@section('header')
    <h1>The Core</h1>
@stop

@section('body')
    <h2 style="margin-top: -25px;">Forgotten Password</h2>
    
    {{ Form::open(['class' => 'form-horizontal', 'autocomplete' => 'off']) }}

        <div class="control-group">
            {{ Form::label('email', 'Email', ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::text('email') }}
            </div>
        </div>

        <div class="control-group">
            {{ Form::label('password', 'New Password', ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::password('password') }}
            </div>
        </div>

        <div class="control-group">
            {{ Form::label('password', 'Confirm Password', ['class' => 'control-label']) }}
            <div class="controls">
                {{ Form::password('confirm_password') }}
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                {{ Form::submit('Request Reset', ['class' => 'btn']) }}
                <p style="margin-top: 30px"><a href="{{ route('login') }}" style="margin-right: 20px;">Back to login</a></p>
            </div>
        </div>

    {{ Form::close() }}

@stop