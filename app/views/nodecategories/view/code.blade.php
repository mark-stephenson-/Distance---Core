<?php

    $syntaxes = array();

    foreach (Config::get('core-code-editor') as $code => $d) {
        $syntaxes[$code] = $d['name'];
    }
?>

<link rel="stylesheet" href="/js/codemirror/lib/codemirror.css">
<script src="/js/codemirror/lib/codemirror.js"></script>

@if ( Config::get('core-code-editor.' . $column->syntax . '.scripts') )
  @foreach ( Config::get('core-code-editor.' . $column->syntax . '.scripts')  as $script )
    <script src="{{ $script }}"></script>
  @endforeach
@endif

{{ Form::textarea('', $data->{$column->name}, ['class' => 'span10', 'id' => 'code']) }}

<script type="text/javascript">
      window.onload = function() {
        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        lineNumbers: true,
        lineWrapping: true,
        readOnly: true,
        mode: "text/html"
      });
      };
    </script>