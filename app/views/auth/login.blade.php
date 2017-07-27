@extends('layouts.auth')

@section('header')
    <h1>The Core</h1>
@stop

@section('body')
    
    {{ Form::open(array('class' => 'form')) }}

        <div class="control-group">
            {{ Form::label('email', 'Email', array('class' => 'control-label')) }}
            <div class="controls">
                {{ Form::text('email', Input::old('email')) }}
            </div>
        </div>

        <div class="control-group">
            {{ Form::label('password', 'Password', array('class' => 'control-label')) }}
            <div class="controls">
                {{ Form::password('password') }}
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <label class="checkbox">
                    {{ Form::checkbox('remember') }} Remember me
                </label>
                {{ Form::submit('Login', array('class' => 'btn')) }}
                <p style="margin-top: 30px"><a href="{{ route('forgot-password') }}">Forgotten your password?</a></p>
            </div>
        </div>

    {{ Form::close() }}

@stop