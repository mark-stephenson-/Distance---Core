<html>
    <head>
        <link rel="stylesheet" type="text/css" href="http://dev/css/app.min.css">
        <style>
            body {
                background: #FFF;
            }
        </style>
    </head>
    <body>
        <div class="title-block">
            <h3>PRASE Reporting</h3>
        </div>

            @include('reporting.partials.report-table')
            @include('reporting.partials.domain-info')

            <h3>Summary</h3>
            @include('reporting.partials.explanation')

            <div id="summary">
                @include('reporting.partials.summary-key')

                @include('reporting.partials.domain-summary')

            </div>
            <div id="comments">
                @include('reporting.partials.positive-comments', ['comments' => $reportData->notes])
            </div>
            <div id="concerns">
                @include('reporting.partials.concerns', ['concerns' => $reportData->concerns])
            </div>
            <div id="5"></div>
            <div id="6"></div>
            <div id="7"></div>
    </body>
</html>