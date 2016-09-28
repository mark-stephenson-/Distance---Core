<div class="clearfix"></div>
{{ Form::open(['method' => 'GET', 'class' => 'form-inline filter-form']) }}
    <div class="span4">
        <div>{{ Form::label('filter', 'Primary Filter') }}</div>
        <div>{{ Form::select('filter', ['preventability' => 'Preventability', 'severity' => 'Severity'], Input::get('filter')) }}</div>
    </div>
    <div class="span4">
        <div>{{ Form::label('preventability', 'Preventability Order') }}</div>
        <div>{{ Form::select('preventability', ['asc' => 'Hard to Easy', 'desc' => 'Easy to Hard'], Input::get('preventability')) }}</div>
    </div>
    <div class="span4">
        <div>{{ Form::label('severity', 'Severity Order') }}</div>
        <div>{{ Form::select('severity', ['asc' => 'Low to High', 'desc' => 'High to Low'], Input::get('severity')) }}</div>
    </div>
{{ Form::close() }}
<br>
