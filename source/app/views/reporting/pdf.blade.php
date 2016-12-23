<html>
    <head>

        <style>
            body {
                background: #FFF;
                margin: 0;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 13px;
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
                height: 30px;
            }

            .key-danger {
                background-color: #8B0000;
                width: 10px;
                height: 10px;
            }

            .bar-danger {
                background: #8B0000;
            }

            .key-warning {
                background-color: #FF0000;
                width: 10px;
                height: 10px;
            }

            .bar-warning {
                background: #FF0000;
            }

            .key-neutral {
                background-color: #FFC200;
                width: 10px;
                height: 10px;
            }

            .progress .bar.bar-neutral {
                background: #FFC200;
            }

            .key-positive {
                background-color: #88E188;
                width: 10px;
                height: 10px;
            }

            .bar-positive {
                background: #88E188;
            }

            .key-success {
                background-color: #006400;
                width: 10px;
                height: 10px;
            }

            .bar-success {
              background: #006400 !important;
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
                background-color: #8B0000;
            }

            .progress .bar-positive {
                background-color: #88E188;
            }

            .progress .bar-neutral {
                background-color: #FFC200;
            }

            .progress .bar-warning {
                background-color: #FF0000;
            }

            .progress .bar-success {
                background-color: #006400 !important;;
            }
        </style>
    </head>
    <body>
        <div class="title-block">
            <h3>PRASE Ward Feedback Report</h3>
        </div>

            <table>
                <tr>
                    <td>
                        @include('reporting.partials.report-table')
                    </td>
                    <td>
                        <img style="width: 200px; float: right;" src="data:image/png;base64,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">
                    </td>
                </tr>
            </table>
            @include('reporting.partials.domain-info')

            <h3>Report Summary</h3>
            @include('reporting.partials.explanation')

            <div style="page-break-before: always;"></div>

            <h3>Overview of the patient measures of safety</h3>
            @include('reporting.partials.summary-key')
            @include('reporting.partials.domain-summary', ['noLimit' => true])

            <h3>General patient reported positive experiences of care received</h3>
            @include('reporting.partials.positive-comments', ['comments' => $reportData->notes, 'noLimit' => true])

            <h3>General patient reported safety concerns</h3>
            @include('reporting.partials.concerns', ['concerns' => new \Illuminate\Support\Collection(array_values((array) $reportData->concerns)), 'noLimit' => true])

            @foreach($reportData->domains as $domain)
                <h3 style="page-break-before: always;">{{ $domain->name }} domain</h3>
                @include('reporting.partials.summary-key')
                @include('reporting.partials.domain-questions', ['noLimit' => true])

                @if (isset($domain->notes))
                    <h3>Patient reported positive experiences of care received</h3>
                    @include('reporting.partials.positive-comments', ['comments' => $domain->notes, 'noLimit' => true])
                @endif
                @if (isset($domain->concerns))
                    <h3>Patient reported safety concerns</h3>
                    @include('reporting.partials.concerns', ['concerns' => new \Illuminate\Support\Collection(array_values((array) $domain->concerns)), 'noLimit' => true])
                @endif
            @endforeach
    </body>
</html>
