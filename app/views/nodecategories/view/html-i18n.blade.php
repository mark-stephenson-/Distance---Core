<?php
    $column_name = $data->{$column->name};
?>

@if ($column_name)
<ul>
    @foreach (I18nHtml::whereKey($column_name)->get() as $translation)
        <?php
            $html = trim($translation->value);

            if ($html) {
                $doc = new DOMDocument();
                $doc->loadHTML($html);
                $imageTags = $doc->getElementsByTagName('img');

                foreach($imageTags as $img) {
                    $img->setAttribute('src', URL::to('file') . '/' . $collection->id . '/' . $translation->lang . '/' . $img->getAttribute('src'));
                }

                $html = $doc->saveHTML();
            }
        ?>
        <li>
            <span>[{{ strtoupper($translation->lang) }}]</span>
            <div class="scroll">
                {{ $html }}
            </div>
        </li>
    @endforeach
</ul>
@else
    N/A
@endif