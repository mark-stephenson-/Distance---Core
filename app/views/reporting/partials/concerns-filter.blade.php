{{ Form::open(['method' => 'GET', 'class' => 'form-inline filter-form']) }}
<div class="span4">
    {{ Form::label('filter', 'Primary Filter') }}
    {{ Form::select('filter', ['preventability' => 'Preventability', 'severity' => 'Severity'], Input::get('filter')) }}
</div>
<div class="span4">
    {{ Form::label('preventability', 'Preventability Order') }}
    {{ Form::select('preventability', ['asc' => 'Hard to Easy', 'desc' => 'Easy to Hard'], Input::get('preventability')) }}
</div>
<div class="span4">
    {{ Form::label('severity', 'Severity Order') }}
    {{ Form::select('severity', ['asc' => 'Low to High', 'desc' => 'High to Low'], Input::get('severity')) }}
</div>
{{ Form::close() }}