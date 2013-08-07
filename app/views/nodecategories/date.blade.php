<?php
    $identifier = uniqid();

    if (!isset($data)) {
        if (!@$column->default) {
            $value = 'today';
        } else {
            $value = $column->default;
        }
    } else {
        $value = @$data->{$column->name};
    }
?>

<script src="/js/datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
<script src="/js/bootstrap/bootstrap-collapse.js"></script>

<script type="text/javascript">
    $(function() {
        $('#{{ $identifier }}').datetimepicker({
            language: 'en-GB',
            pickSeconds: false
        });
    });
</script>

<div id="{{ $identifier }}" class="input-append span8">
    {{ Form::text('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, date('d/m/Y h:i', strtotime($value))), ['class' => 'span8 validate-date datepicker', 'data-format' => 'dd/MM/yyyy hh:mm']) }}
    <span class="add-on">
        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
    </span>
</div>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif