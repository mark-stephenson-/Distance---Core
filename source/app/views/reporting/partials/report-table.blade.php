<table class="table">
    <tbody>
    <tr>
        <td class="table-key"><strong>No of Patient Responses</strong></td>
        <td>{{ $reportData->submissions->total }} ({{ $reportData->submissions->male }} Male, {{ $reportData->submissions->female }} Female, {{ $reportData->submissions->total - $reportData->submissions->female -$reportData->submissions->male }} Not recorded)</td>
    </tr>
    <tr>
        <td class="table-key"><strong>Data Collection Period</strong></td>
        <td>{{ $start }} - {{ $end }}</td>
    </tr>
    <tr>
        <td class="table-key"><strong>Trust</strong></td>
        <td>{{ $reportData->trust }}</td>
    </tr>
    <tr>
        <td class="table-key"><strong>Hospital</strong></td>
        <td>{{ $reportData->hospital  }}</td>
    </tr>
    <tr>
        <td class="table-key"><strong>Ward</strong></td>
        <td>{{ $reportData->ward }}</td>
    </tr>
    </tbody>
</table>
