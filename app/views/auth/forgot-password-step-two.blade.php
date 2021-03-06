@extends('layouts.auth')

@section('header')
    <h1>PRASE</h1>
@stop

@section('body')
    <h2 style="margin-top: -25px;">Forgotten Password</h2>
    
    {{ Form::open(array('class' => 'form-horizontal', 'autocomplete' => 'off')) }}

        <div class="control-group">
            {{ Form::label('email', 'Email', array('class' => 'control-label')) }}
            <div class="controls">
                <p style="margin-top: 5px;">{{ $user->email }}</p>
            </div>
        </div>

        <div class="control-group">
            {{ Form::label('password', 'New Password', array('class' => 'control-label')) }}
            <div class="controls">
                {{ Form::password('password') }}
            </div>
        </div>

        <div class="control-group">
            {{ Form::label('password', 'Confirm Password', array('class' => 'control-label')) }}
            <div class="controls">
                {{ Form::password('confirm_password') }}
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                {{ Form::submit('Submit', array('class' => 'btn')) }}
            </div>
        </div>

    {{ Form::close() }}

@stop