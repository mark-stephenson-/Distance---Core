<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">Domain</th>
            <th style="width: 60%;"> </th>
            <th style="width: 20%;">Notes</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportData->domains as $domainId => $domain)
        <tr>
            @if (isset($noLimit))
                <td>{{ $domain->name}}</td>
            @else
                <td data-toggle="tooltip" data-placement="top" title="{{ $domain->name }}"><a href="{{ Request::url() }}?domain={{ $domainId }}">{{ str_limit($domain->name, 20) }}</a></td>
            @endif
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
                    <div class="bar bar-danger" style="width: {{ floor(($domain->summary->{"1"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ $domain->summary->{"1"} }}"> </div><!--
                    !--><div class="bar bar-warning" style="width: {{ floor(($domain->summary->{"2"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ $domain->summary->{"2"} }}"> </div><!--
                    !--><div class="bar bar-neutral" style="width: {{ floor(($domain->summary->{"3"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ $domain->summary->{"3"} }}"> </div><!--
                    !--><div class="bar bar-positive" style="width: {{ floor(($domain->summary->{"4"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ $domain->summary->{"4"} }}"> </div><!--
                    !--><div class="bar bar-success" style="width: {{ floor(($domain->summary->{"5"}/$total) * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ $domain->summary->{"5"} }}"> </div><!--
                    !-->
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
                @if($somethingGood == true)
                    <i class="icon-check"></i>
                @endif

                @if($concerns == true)
                    <i class="icon-exclamation"></i>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>