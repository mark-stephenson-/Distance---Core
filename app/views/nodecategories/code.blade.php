<?php
    $identifier = uniqid();

    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

<link rel="stylesheet" href="/js/codemirror/lib/codemirror.css">
<script src="/js/codemirror/lib/codemirror.js"></script>

@if ( Config::get('core-code-editor.' . $column->syntax . '.scripts') )
  @foreach ( Config::get('core-code-editor.' . $column->syntax . '.scripts')  as $script )
    <script src="{{ $script }}"></script>
  @endforeach
@endif

{{ Form::textarea('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, str_replace('&', '&amp;', $value)), array('class' => 'span10', 'id' => 'code-' . $identifier)) }}

<script type="text/javascript">
        CodeMirror.fromTextArea(document.getElementById("code-{{ $identifier }}"), {
        lineNumbers: true,
        lineWrapping: true,
        mode: "{{ Config::get('core-code-editor.' . $column->syntax . '.mode') }}"
      });
    </script>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif