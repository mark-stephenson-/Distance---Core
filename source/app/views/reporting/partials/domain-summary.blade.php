<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">Domain</th>
            <th style="width: 70%;"> </th>
            <th style="width: 10%;">Notes</th>
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
                        <table style="width:100%;">
                          <tr style="padding: 0;">
                            @if(($domain->summary->{"1"} == 0))
                            <td class="bar-danger" style="display: none;"></td>
                            @else
                            <td class="bar-danger" style="padding: 0;width: {{ ($domain->summary->{"1"}/$total) * 100 }}%;"><div title="Negative-: {{ $domain->summary->{"1"} }}"></div></td>
                            @endif

                            @if(($domain->summary->{"2"} == 0))
                            <td class="bar-warning" style="display: none;"></td>
                            @else
                            <td class="bar-warning" style="padding: 0;width: {{ ($domain->summary->{"2"}/$total) * 100 }}%;"><div title="Negative: {{ $domain->summary->{"2"} }}"></div></td>
                            @endif

                            @if(($domain->summary->{"3"} == 0))
                            <td class="bar-neutral" style="display:none;"></td>
                            @else
                            <td class="bar-neutral" style="padding: 0;width: {{ ($domain->summary->{"3"}/$total) * 100 }}%;"><div title="Neutral: {{ $domain->summary->{"3"} }}">&nbsp;</div></td>
                            @endif

                            @if(($domain->summary->{"4"} == 0))
                            <td class="bar-positive" style="display:none;"></td>
                            @else
                            <td class="bar-positive" style="padding: 0;width: {{ ($domain->summary->{"4"}/$total) * 100 }}%;"><div title="Positive: {{ $domain->summary->{"4"} }}">&nbsp;</div></td>
                            @endif

                            @if(($domain->summary->{"5"} == 0))
                            <td class="bar-success" style="display:none;"></td>
                            @else
                            <td class="bar-success" style="padding: 0;width: {{ ($domain->summary->{"5"}/$total) * 100 }}%;"><div title="Positive+: {{ $domain->summary->{"5"} }}">&nbsp;</div></td>
                            @endif
                          </tr>
                        </table>
                    @else
                        <div class="bar" style="width: 0%;" data-toggle="tooltip" data-placement="top"> </div>
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
                <img style="width:14px" data-toggle="tooltip" data-placement="top" title="No. of positive comments: {{ $somethingGood }}" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAMAAAAolt3jAAAAaVBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnbPKNAAAAInRSTlMAAQIDBAUHDRw7REdJSmh0foCCjJGSmJqbnaO8wMjP2fX3TxCUVQAAAGxJREFUCB1dwdsWgVAUQNG1t3INoeSus/7/I41BT+bkT9WNfpVVLKFvMn7y5oaSTM56xOTnpENg1vcdcNAhwGy1jb0OAZjzl771EgvApH6q12AETKgeXoMQKAnM1gE5At02J00H1F3xq/QV/z7AMQj8tBlJbwAAAABJRU5ErkJggg==" />
                @endif

                @if($concerns)
                <img style="width:14px" data-toggle="tooltip" data-placement="top" title="No. of concerns: {{ $concerns }}" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAMAAAAolt3jAAAAS1BMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADmYDp0AAAAGHRSTlMAAQMICR4xM0B7f4mOnaOvt8jK2dzr7f3ow3urAAAAR0lEQVQIHXXBOQKAIBAEwUFF8caT/v9LRQM2skqfNoROhSOrVJA5FQmQuSDJHHDLbHDKRNhlBpj0q/a+kZlhlVkgyvQw6vUAriQD0hZ+oH8AAAAASUVORK5CYII=" />
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
