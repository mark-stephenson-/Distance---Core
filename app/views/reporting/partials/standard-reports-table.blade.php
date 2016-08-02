<table class="table table-bordered data-table">
    <thead>
        <th>Report Generated</th>
        <th>Trust</th>
        <th>Hospital</th>
        <th>Ward</th>
        <th>Responses Date Range</th>
    </thead>
    <tbody>
        @if(!empty($standardReports))
            @foreach($standardReports as $report)
                <tr>
                    <td>
                        <a href="{{ route('reporting.view', array('id' => $report->fileName, 'type' => 'standard')) }}">{{ $report->generated_at }}</a>
                    </td>
                    <td>{{ $report->trust }}</td>
                    <td>{{ $report->hospital }}</td>
                    <td>{{ $report->ward }}</td>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $report->dates->start)->format('d/m/Y') }} - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $report->dates->end)->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>
                    There are no standard reports available.
                </td>
            </tr>
        @endif
    </tbody>
</table>