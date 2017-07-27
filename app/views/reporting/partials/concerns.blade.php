@if(!$concerns->isEmpty())
    <table class="table">
        <thead>
            <tr>
                @if (isset($concerns->first()->question))
                    <th>Question</th>
                @endif
                <th>Safety Concern</th>
                <th>Patient Rated Preventability</th>
                <th>Patient Rated Severity</th>
            </tr>
        </thead>
        <tbody>
          
        <?php $lastQuestion = "" ?>
        @foreach($concerns as $concern)
            <tr>
                @if (isset($concern->question))
                    @if($lastQuestion != $concern->question)
                        <?php $lastQuestion = $concern->question ?>
                        @if (isset($noLimit))
                            <td>{{ $concern->question}}</td>
                        @else
                            <td data-toggle="tooltip" data-placement="top" title="{{ $concern->question }}">{{ (!isset($noLimit)) ? str_limit($concern->question, 20) : $concern->question }}</td>
                        @endif
                    @else
                        <td></td>
                    @endif
                @endif
                <td>{{ $concern->text }}</td>
                <td>
                    @if(isset($concern->preventability) && $concern->preventability < 2)
                        Not Preventable
                    @elseif(isset($concern->preventability) && $concern->preventability == 2)
                        May be preventable
                    @elseif(isset($concern->preventability) && $concern->preventability == 3)
                        Preventable
                    @else
                        Unknown
                    @endif
                </td>
                <td>
                    @if(!isset($concern->severity))
                        <p style="color: #e45752">Unknown</p>
                    @elseif($concern->severity < 4)
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
@else
    <div class="alert alert-success">No specific patient experience was reported relating to these questions</div>
@endif
