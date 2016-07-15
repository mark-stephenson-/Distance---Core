<table class="table">
    <thead>
        <tr>
            <th>Patient Comments</th>
        </tr>
    </thead>
    <tbody>
    @foreach($comments as $note)
        <tr>
            <td>{{ $note->text }}</td>
        </tr>
    @endforeach
    </tbody>
</table>