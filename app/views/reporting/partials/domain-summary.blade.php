<table class="table">
    <thead>
    <th style="width: 20%;">Domain</th>
    <th style="width: 60%;"></th>
    <th style="width: 20%;">Notes</th>
    </thead>
    <tbody>
    @foreach($reportData->domains as $domain)
        <tr>
            <td>{{ $domain->name }}</td>
            <td>
                <div class="progress">
                    <div class="bar bar-danger" style="width: {{ $domain->summary->{"1"} }}%;"></div>
                    <div class="bar bar-warning" style="width: {{ $domain->summary->{"2"} }}%;"></div>
                    <div class="bar bar-neutral" style="width: {{ $domain->summary->{"3"} }}%;"></div>
                    <div class="bar bar-positive" style="width: {{ $domain->summary->{"4"} }}%;"></div>
                    <div class="bar bar-success" style="width: {{ $domain->summary->{"5"} }}%;"></div>
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