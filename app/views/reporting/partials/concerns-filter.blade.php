{{ Form::open(['method' => 'GET', 'class' => 'form-inline']) }}
<div class="span4">
    {{ Form::label('filter', 'Primary Filter') }}
    {{ Form::select('filter', ['preventability' => 'Preventability', 'severity' => 'Severity']) }}
</div>
<div class="span4">
    {{ Form::label('preventability', 'Preventability Order') }}
    {{ Form::select('preventability', ['not' => 'Not Preventable', 'maybe' => 'May be preventable', 'preventable' => 'Preventable', 'unknown' => 'Unknown (Don\'t know'], 'not') }}
</div>
<div class="span4">
    {{ Form::label('severity', 'Severity Order') }}
    {{ Form::select('severity', ['low' => '1 - 3 Low', 'medium' => '4 - 6 Medium', 'high' => '7 - 10 High'], 'low') }}
</div>
{{ Form::close() }}