<html>
    <head>
        <style>
            body {
                background: #FFF;
                margin: 0;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 14px;
                line-height: 20px;
                color: #333333;
            }
            .table {
                width: 100%;
                margin-bottom: 20px;
                border-collapse: collapse;
                border-spacing: 0;
                white-space: normal;
                line-height: normal;
                text-align: start;
                border-color: grey;
            }
            .table th, .table td {
                padding: 8px;
                line-height: 20px;
                text-align: left;
                vertical-align: top;
                border-top: 1px solid #dddddd;
            }
            .table-key {
                background-color: #f5f5f5;
            }

            .colour {
                display: inline-block;
            }

            .key-outer {
                height: 50px;
            }

            .key-danger {
                background-color: #dd514c;
                width: 10px;
                height: 10px;
            }

            .bar-danger {
                background: #dd514c;
            }

            .key-warning {
                background-color: #faa732;
                width: 10px;
                height: 10px;
            }

            .bar-warning {
                background: #faa732;
            }

            .key-neutral {
                background-color: #fbfa37;
                width: 10px;
                height: 10px;
            }

            .progress .bar.bar-neutral {
                background: #fbfa37;
            }

            .key-positive {
                background-color: #93c476;
                width: 10px;
                height: 10px;
            }

            .bar-positive {
                background: #93c476;
            }

            .key-success {
                background-color: #009c0f;
                width: 10px;
                height: 10px;
            }

            .bar-success {
                background: #009c0f;
            }

            .progress {
                overflow: hidden;
                width: 100%;
                height: 20px;
                margin-bottom: 20px;
                background-color: #f7f7f7;
            }

            .bar {
                height: 20px;
                display: inline-block;
            }

            .progress .bar-danger {
                background-color: #dd514c;
            }

            .progress .bar-positive {
                background-color: #0e90d2;
            }

            .progress .bar-neutral {
                background-color: #fbfa37;
            }

            .progress .bar-warning {
                background-color: #faa732;
            }
        </style>
    </head>
    <body>
        <div class="title-block">
            <h3>PRASE Reporting</h3>
        </div>

            @include('reporting.partials.report-table')
            @include('reporting.partials.domain-info')

            <h3>Report Summary</h3>
            @include('reporting.partials.explanation')

            @include('reporting.partials.summary-key')

            @include('reporting.partials.domain-summary', ['noLimit' => true])

            @include('reporting.partials.positive-comments', ['comments' => $reportData->notes, 'noLimit' => true])
            @include('reporting.partials.concerns', ['concerns' => new \Illuminate\Support\Collection(array_values((array) $reportData->concerns)), 'noLimit' => true])

            @foreach($reportData->domains as $domain)
                <h3>Report Summary > {{ $domain->name }}</h3>

                @include('reporting.partials.summary-key')
                @include('reporting.partials.domain-questions', ['noLimit' => true])
                @if (isset($domain->notes))
                    @include('reporting.partials.positive-comments', ['comments' => $domain->notes, 'noLimit' => true])
                @endif
                @if (isset($domain->concerns))
                    @include('reporting.partials.concerns', ['concerns' => new \Illuminate\Support\Collection(array_values((array) $domain->concerns)), 'noLimit' => true])
                @endif
            @endforeach
    </body>
</html>