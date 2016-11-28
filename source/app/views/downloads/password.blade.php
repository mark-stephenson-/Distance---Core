<!DOCTYPE html>
<html>
    <head>
        <title>Install NHS Prase</title>

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <link rel="stylesheet" href="/css/download.css" />
    </head>

    <body>
        <div class="banner">
            Install NHS Prase
        </div>

        <div class="prase"></div>

        <div class="app-info">
            <div class="padding">
                <img src="/images/prase-icon.png" alt="" height="45px" />
                <h1>NHS Prase</h1>
                <p class="version">Version {{ $version->version }}</p>
            </div>
        </div>

        <div class="notes">

            <p>Please enter your password</p>

            @if (isset($error))
                <p class="error">{{ $error }}</p>
            @endif

            {{ Form::open(array()) }}

                {{ Form::password('password') }}
                {{ Form::submit('Go', array('class' => 'button')) }}

            {{ Form::close() }}

        </div>

    </body>
</html>
