@if(!empty($comments))
    <table class="table">
        <thead>
            <tr>
                @if ($firstComment = reset($comments) and isset($firstComment->question))
                    <th>Question</th>
                @endif
                <th>Patient Comments</th>
            </tr>
        </thead>
        <tbody>

          @if(isset($firstComment->question))
            <?php $lastQuestion = $firstComment->question ?>
          @endif
            @foreach($comments as $note)
                <tr>
                    @if (isset($note->question))
                        @if (isset($noLimit))
                            @if($lastQuestion != $note->question || $lastQuestion == $firstComment->question)
                            <td>{{ $note->question }}</td>
                            <?php $lastQuestion = $note->question ?>
                              @else
                            <td></td>
                            @endif
                        @else
                            <td data-toggle="tooltip" data-placement="top" title="{{ $note->question }}">{{ str_limit($note->question, 20) }}</td>
                        @endif
                    @endif
                    <td>{{ $note->text }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-success">No specific patient experience was reported relating to these questions</div>
@endif
