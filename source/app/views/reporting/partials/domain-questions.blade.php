<table class="table">
    <thead>
    <tr>
        <th style="width: 20%;">Question</th>
        <th style="width: 70%;"> </th>
        <th style="width: 10%;">Notes</th>
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
                        <table style="width:100%;">
                          <tr style="padding: 0;">
                            <td class="bar-danger" style="padding: 0;width: {{ ($domain->summary->{"1"}/$total) * 100 }}%;"><div title="Negative-: {{ $domain->summary->{"1"} }}">&nbsp;</div></td>
                            <td class="bar-warning" style="padding: 0;width: {{ ($domain->summary->{"2"}/$total) * 100 }}%;"><div title="Negative: {{ $domain->summary->{"2"} }}">&nbsp;</div></td>
                            <td class="bar-neutral" style="padding: 0;width: {{ ($domain->summary->{"3"}/$total) * 100 }}%;"><div title="Neutral: {{ $domain->summary->{"3"} }}">&nbsp;</div></td>
                            <td class="bar-positive" style="padding: 0;width: {{ ($domain->summary->{"4"}/$total) * 100 }}%;"><div title="Positive: {{ $domain->summary->{"4"} }}">&nbsp;</div></td>
                            <td class="bar-success" style="padding: 0;width: {{ ($domain->summary->{"5"}/$total) * 100 }}%;"><div title="Positive+: {{ $domain->summary->{"5"} }}">&nbsp;</div></td>
                          </tr>
                        </table>
                    @else
                        <div class="bar" style="width: 0%;" data-toggle="tooltip" data-placement="top"> </div>
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
                        <table style="width:100%;">
                          <tr style="padding: 0;">
                            <td class="bar-danger" style="padding: 0;width: {{ (@$question->answers->{"1"}/$total) * 100 }}%;"><div title="Negative-: {{ @$question->answers->{"1"} }}">&nbsp;</div></td>
                            <td class="bar-warning" style="padding: 0;width: {{ (@$question->answers->{"2"}/$total) * 100 }}%;"><div title="Negative: {{ @$question->answers->{"2"} }}">&nbsp;</div></td>
                            <td class="bar-neutral" style="padding: 0;width: {{ (@$question->answers->{"3"}/$total) * 100 }}%;"><div title="Neutral: {{ @$question->answers->{"3"} }}">&nbsp;</div></td>
                            <td class="bar-positive" style="padding: 0;width: {{ (@$question->answers->{"4"}/$total) * 100 }}%;"><div title="Positive: {{ @$question->answers->{"4"} }}">&nbsp;</div></td>
                            <td class="bar-success" style="padding: 0;width: {{ (@$question->answers->{"5"}/$total) * 100 }}%;"><div title="Positive+: {{ @$question->answers->{"5"} }}">&nbsp;</div></td>
                          </tr>
                        </table>
                    @else
                        <div class="bar" style="width: 0%;" data-toggle="tooltip" data-placement="top"> </div>
                    @endif
                </div>
            </td>
            <td>
                @if(isset($question->notes))
                <img style="width:14px" data-toggle="tooltip" data-placement="top" title="No. of positive comments: {{ count($question->notes) }}" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAMAAAAolt3jAAAAaVBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnbPKNAAAAInRSTlMAAQIDBAUHDRw7REdJSmh0foCCjJGSmJqbnaO8wMjP2fX3TxCUVQAAAGxJREFUCB1dwdsWgVAUQNG1t3INoeSus/7/I41BT+bkT9WNfpVVLKFvMn7y5oaSTM56xOTnpENg1vcdcNAhwGy1jb0OAZjzl771EgvApH6q12AETKgeXoMQKAnM1gE5At02J00H1F3xq/QV/z7AMQj8tBlJbwAAAABJRU5ErkJggg==" />
                @endif

                @if(isset($question->concerns))
                <img style="width:14px" data-toggle="tooltip" data-placement="top" title="No. of concerns: {{ count($question->concerns) }}" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAMAAAAolt3jAAAAS1BMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADmYDp0AAAAGHRSTlMAAQMICR4xM0B7f4mOnaOvt8jK2dzr7f3ow3urAAAAR0lEQVQIHXXBOQKAIBAEwUFF8caT/v9LRQM2skqfNoROhSOrVJA5FQmQuSDJHHDLbHDKRNhlBpj0q/a+kZlhlVkgyvQw6vUAriQD0hZ+oH8AAAAASUVORK5CYII=" />
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
