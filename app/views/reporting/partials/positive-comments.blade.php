<table class="table">
    <thead>
    <th>Patient Comments</th>
    </thead>
    <tbody>
    @foreach($comments as $note)
        <tr>
            <td>{{ $note->text }}</td>
        </tr>
    @endforeach
    </tbody>
</table>