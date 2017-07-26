<?php
    $html = trim($data->{$column->name});
    $language = "en";

    if ($html) {
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $imageTags = $doc->getElementsByTagName('img');

        foreach($imageTags as $img) {
            $img->setAttribute('src', URL::to('file') . '/' . $collection->id . '/' . $language . '/' . $img->getAttribute('src'));
        }

        $html = $doc->saveHTML();
    }
?>

<div class="scroll">
    {{ $html }}
</div>