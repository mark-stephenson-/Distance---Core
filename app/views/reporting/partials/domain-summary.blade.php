<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">Domain</th>
            <th style="width: 60%;"> </th>
            <th style="width: 20%;">Notes</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportData->domains as $domain)
        <tr>
            <td>{{ $domain->name }}</td>
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
                    <i class="fa fa-check" aria-hidden="true"></i>
                @endif

                @if($concerns == true)
                    <i class="fa fa-exclamation" aria-hidden="true"></i>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>