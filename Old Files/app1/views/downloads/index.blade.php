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

        <div class="ignaz"></div>

        <div class="app-info">
            <div class="padding">
                <img src="/images/prase-icon.png" alt="" height="45px" />
                <h1>NHS Prase</h1>
                <p class="version">Version {{ $version->version }}</p>

                <a href="{{ $version->downloadUrl() }}" id="downloadButton" class="button">Install</a>
            </div>
        </div>

        <div class="notes">

            @if ( $version->platform == "android" )
                <div class="column" style="width: 100%">
                    <p>Please make sure you “allow installation of non-Market applications” in your Settings to install NHS Prase.</p>
                    <p>If you have difficulties downloading the app, please try visiting this webpage again with FireFox web-browser for Android (freely available in Google Market).</p>
                </div>
            @endif

            {{--
            @if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false or strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false)
            <div class="column">
                <h3>Provisional Profile</h3>
                <p>This is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.</p>
                <a href="{{ $version->downloadUrl('profile') }}" class="button">Install Provisioning Profile</a>
            </div>
            @endif
            --}}

            <div class="column">
                <h3>Release Notes</h3>
                <p>{{ str_replace("\n", "</p><p>", $version->release_notes) }}</p>
            </div>

            <div class="column">
                <h3>Details</h3>
                <b>Release Date:</b> {{ date('F j, Y', strtotime($version->created_at)) }}<br />
                <!-- <b>Minimum OS:</b> 5.0<br /> -->
                <b>Device:</b> {{ $version->fancyPlatform() }}<br />
                @if ($version->environment == 'testing')
                    <b>Environment:</b> Testing<br />
                @endif
                @if ( $version->platform == "windows" )
                    <b>Application Enrollment Token:</b> <a href="{{ $version->downloadUrl('certificate') }}">Download</a><br />
                @endif
            </div>

        </div>

    </body>
</html>
