<?php
    $html = $data->{$column->name};

    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $imageTags = $doc->getElementsByTagName('img');

    foreach($imageTags as $img) {
        $img->setAttribute('src', URL::to('file') . '/' . $collection->id . '/' . $img->getAttribute('src'));
    }

    $html = $doc->saveHTML();
?>

<div class="scroll">
    {{ $html }}
</div>