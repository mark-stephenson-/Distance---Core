<table class="table">
    <thead>
        <tr>
            <th>Patient Concern</th>
            <th>Preventability</th>
            <th>Severity</th>
        </tr>
    </thead>
    <tbody>
    @foreach($concerns as $concern)
        <tr>
            <td>{{ $concern->text }}</td>
            <td>
                @if($concern->preventability < 2)
                    Not Preventable
                @elseif($concern->preventability == 2)
                    May be preventable
                @elseif($concern->preventability == 3)
                    Preventable
                @else
                    Unknown
                @endif
            </td>
            <td>
                @if($concern->severity < 4)
                    <p style="color: #59af59">Low</p>
                @elseif($concern->severity >= 4 && $concern->severity <7)
                    <p style="color: #f9a124">Medium</p>
                @else
                    <p style="color: #e45752">High</p>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>