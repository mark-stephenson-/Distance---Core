<?php

return array(
    'xml' => array(
        'name' => 'XML',
        'mode' => 'application/xml',
        'scripts' => array(
            "/js/codemirror/addon/hint/xml-hint.js",
            "/js/codemirror/mode/xml/xml.js",
            "/js/codemirror/mode/htmlmixed/htmlmixed.js"
        ),
    ),

    'html' => array(
        'name' => 'HTML',
        'mode' => 'text/html',
        'scripts' => array(
            "/js/codemirror/addon/hint/xml-hint.js",
            "/js/codemirror/addon/hint/html-hint.js",
            "/js/codemirror/mode/xml/xml.js",
            "/js/codemirror/mode/css/css.js",
            "/js/codemirror/mode/htmlmixed/htmlmixed.js"
        ),
    ),

    'json' => array(
        'name' => 'JSON',
        'mode' => 'application/json',
        'scripts' => array(
            "/js/codemirror/addon/hint/javascript-hint.js",
            "/js/codemirror/mode/javascript/javascript.js",
        ),
    ),
);