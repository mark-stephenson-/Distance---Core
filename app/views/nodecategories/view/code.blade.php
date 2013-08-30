<?php
    $identifier = uniqid();
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

{{ Form::textarea('', str_replace('&', '&amp;', $data->{$column->name}), array('class' => 'span10', 'id' => 'code-' . $identifier)) }}

<script type="text/javascript">
        CodeMirror.fromTextArea(document.getElementById("code-{{ $identifier }}"), {
        lineNumbers: true,
        lineWrapping: true,
        readOnly: true,
        mode: "{{ Config::get('core-code-editor.' . $column->syntax . '.mode') }}"
      });
    </script>