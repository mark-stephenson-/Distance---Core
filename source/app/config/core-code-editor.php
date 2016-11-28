<?php

return array(
    'css' => array(
        'name' => 'CSS',
        'mode' => 'text/css',
        'scripts' => array(
            "/js/codemirror/mode/css/css.js"
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

    'javascript' => array(
        'name' => 'JavaScript',
        'mode' => 'text/javascript',
        'scripts' => array(
            "/js/codemirror/addon/hint/javascript-hint.js",
            "/js/codemirror/mode/javascript/javascript.js",
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

    'xml' => array(
        'name' => 'XML',
        'mode' => 'application/xml',
        'scripts' => array(
            "/js/codemirror/addon/hint/xml-hint.js",
            "/js/codemirror/mode/xml/xml.js",
            "/js/codemirror/mode/htmlmixed/htmlmixed.js"
        ),
    ),
);