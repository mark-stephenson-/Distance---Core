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
                    <div class="bar bar-danger" style="width: {{ floor(($domain->summary->{"1"}/$total) * 100) }}%;"> </div><div class="bar bar-warning" style="width: {{ floor(($domain->summary->{"2"}/$total) * 100) }}%;"> </div><div class="bar bar-neutral" style="width: {{ floor(($domain->summary->{"3"}/$total) * 100) }}%;"> </div><div class="bar bar-positive" style="width: {{ floor(($domain->summary->{"4"}/$total) * 100) }}%;"> </div><div class="bar bar-success" style="width: {{ floor(($domain->summary->{"5"}/$total) * 100) }}%;"> </div>
                </div>
            </td>
            <td></td>
        </tr>
    @foreach($domain->questions as $question)
        <tr>
            <td>{{ $question->text }}</td>
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
                    <div class="bar bar-danger" style="width: {{ floor((@$question->answers->{"1"}/$total) * 100) }}%;"> </div><div class="bar bar-warning" style="width: {{ floor((@$question->answers->{"2"}/$total) * 100) }}%;"> </div><div class="bar bar-neutral" style="width: {{ floor((@$question->answers->{"3"}/$total) * 100) }}%;"> </div><div class="bar bar-positive" style="width: {{ floor((@$question->answers->{"4"}/$total) * 100) }}%;"> </div><div class="bar bar-success" style="width: {{ floor((@$question->answers->{"5"}/$total) * 100) }}%;"> </div>
                </div>
            </td>
            <td>
                <?php
                $somethingGood = false;
                $concerns = false;

                foreach($domain->questions as $question) {
                    if(isset($question->notes)) {
                        $somethingGood = true;
                    }

                    if(isset($question->concerns)) {
                        $concerns = true;
                    }
                }
                ?>
                {{--@if($somethingGood == true)--}}
                    {{--<i class="fa fa-check" aria-hidden="true"></i>--}}
                {{--@endif--}}

                {{--@if($concerns == true)--}}
                    {{--<i class="fa fa-exclamation" aria-hidden="true"></i>--}}
                {{--@endif--}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>