<table class="table">
    <tbody>
    <tr>
        <td class="table-key"><strong>Submissions</strong></td>
        <td>{{ $reportData->submissions->total }} ({{ $reportData->submissions->male }} Male, {{ $reportData->submissions->female }} Female)</td>
    </tr>
    <tr>
        <td class="table-key"><strong>Response Date Range</strong></td>
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