<table class="table">
    <thead>
    <tr>
        <th style="width: 20%;">Question</th>
        <th style="width: 60%;"> </th>
        <th style="width: 20%;">Notes</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <th>Summary</th>
            <td>
                <div class="progress">
                    <?php
                    $total = 0;
                    foreach($domain->summary as $key => $val) {
                        if ($key > 0) {
                            $total += $val;
                        }
                    }
                    ?>
                    @if($total > 0)
                        <div class="bar bar-danger" style="width: {{ floor(($domain->summary->{"1"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ $domain->summary->{"1"} }}"> </div>
                        <div class="bar bar-warning" style="width: {{ floor(($domain->summary->{"2"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ $domain->summary->{"2"} }}"> </div>
                        <div class="bar bar-neutral" style="width: {{ floor(($domain->summary->{"3"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ $domain->summary->{"3"} }}"> </div>
                        <div class="bar bar-positive" style="width: {{ floor(($domain->summary->{"4"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ $domain->summary->{"4"} }}"> </div>
                        <div class="bar bar-success" style="width: {{ floor(($domain->summary->{"5"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ $domain->summary->{"5"} }}"> </div>
                    @else
                        <div class="bar bar-danger" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ $domain->summary->{"1"} }}"> </div>
                        <div class="bar bar-warning" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ $domain->summary->{"2"} }}"> </div>
                        <div class="bar bar-neutral" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ $domain->summary->{"3"} }}"> </div>
                        <div class="bar bar-positive" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ $domain->summary->{"4"} }}"> </div>
                        <div class="bar bar-success" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ $domain->summary->{"5"} }}"> </div>
                    @endif
                </div>
            </td>
            <td></td>
        </tr>
    @foreach($domain->questions as $question)
        <tr>
            @if (isset($noLimit))
                <td>{{ $question->text}}</td>
            @else
                <td data-toggle="tooltip" data-placement="top" title="{{ $question->text }}">{{ str_limit($question->text, 20) }}</td>
            @endif
            <td>
                <div class="progress">
                    <?php
                    $total = 0;

                    foreach($question->answers as $key => $val) {
                        if ($key > 0) {
                            $total += $val;
                        }
                    }
                    ?>
                    @if($total > 0)
                        <div class="bar bar-danger" style="width: {{ floor((@$question->answers->{"1"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ @$question->answers->{"1"} }}"> </div>
                        <div class="bar bar-warning" style="width: {{ floor((@$question->answers->{"2"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ @$question->answers->{"2"} }}"> </div>
                        <div class="bar bar-neutral" style="width: {{ floor((@$question->answers->{"3"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ @$question->answers->{"3"} }}"> </div>
                        <div class="bar bar-positive" style="width: {{ floor((@$question->answers->{"4"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ @$question->answers->{"4"} }}"> </div>
                        <div class="bar bar-success" style="width: {{ floor((@$question->answers->{"5"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ @$question->answers->{"5"} }}"> </div>
                    @else
                        <div class="bar bar-danger" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ @$question->answers->{"1"} }}"> </div>
                        <div class="bar bar-warning" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ @$question->answers->{"2"} }}"> </div>
                        <div class="bar bar-neutral" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ @$question->answers->{"3"} }}"> </div>
                        <div class="bar bar-positive" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ @$question->answers->{"4"} }}"> </div>
                        <div class="bar bar-success" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ @$question->answers->{"5"} }}"> </div>
                    @endif
                </div>
            </td>
            <td>

                @if(isset($question->notes))
                        <i class="icon-check" data-toggle="tooltip" data-placement="top" title="No. of positive comments: {{ count($question->notes) }}"></i>
                @endif

                @if(isset($question->concerns))
                        <i class="icon-exclamation" data-toggle="tooltip" data-placement="top" title="No. of concerns: {{ count($question->concerns) }}"></i>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>