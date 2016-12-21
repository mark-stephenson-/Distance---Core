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
                <td data-toggle="tooltip" data-placement="top" title="{{ $domain->name }}"><a href="{{ Request::url() }}?domain={{ $domainId }}&type={{ Input::get('type') }}">{{ str_limit($domain->name, 20) }}</a></td>
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
                    @if($total > 0)
                        <div class="bar bar-danger" style="width: {{ ($domain->summary->{"1"}/$total) * 100 }}%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ $domain->summary->{"1"} }}"> </div>
                        <div class="bar bar-warning" style="width: {{ ($domain->summary->{"2"}/$total * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ $domain->summary->{"2"} }}"> </div>
                        <div class="bar bar-neutral" style="width: {{ ($domain->summary->{"3"}/$total * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ $domain->summary->{"3"} }}"> </div>
                        <div class="bar bar-positive" style="width: {{ ($domain->summary->{"4"}/$total * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ $domain->summary->{"4"} }}"> </div>
                        <div class="bar bar-success" style="width: {{ ($domain->summary->{"5"}/$total * 100) }}%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ $domain->summary->{"5"} }}"> </div>
                    @else
                        <div class="bar bar-danger" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Negative-: {{ $domain->summary->{"1"} }}"> </div>
                        <div class="bar bar-warning" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Negative: {{ $domain->summary->{"2"} }}"> </div>
                        <div class="bar bar-neutral" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Neutral: {{ $domain->summary->{"3"} }}"> </div>
                        <div class="bar bar-positive" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Positive: {{ $domain->summary->{"4"} }}"> </div>
                        <div class="bar bar-success" style="width: 0%;" data-toggle="tooltip" data-placement="top" title="Positive+: {{ $domain->summary->{"5"} }}"> </div>
                    @endif
                </div>
            </td>
            <td>
                <?php
                $somethingGood = 0;
                $concerns = 0;

                foreach($domain->questions as $question) {
                    if(isset($question->notes)) {
                        $somethingGood += count($question->notes);
                    }
                    if(isset($question->concerns)) {
                        $concerns += count($question->concerns);
                    }
                }
                ?>
                @if($somethingGood)
                    <i class="icon-check" data-toggle="tooltip" data-placement="top" title="No. of positive comments: {{ $somethingGood }}"></i>
                @endif

                @if($concerns)
                    <i class="icon-exclamation" data-toggle="tooltip" data-placement="top" title="No. of concerns: {{ $concerns }}"></i>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
