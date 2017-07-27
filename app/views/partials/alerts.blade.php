@if ($successes = Session::get('successes'))
    <div class="alert alert-success">
        {{ implode('<br />', $successes->all()) }}
    </div>
@endif

@if ($notices = Session::get('notices'))
    <div class="alert info">
        {{ implode('<br />', $notices->all()) }}
    </div>
@endif

@if ($errors = Session::get('errors'))
    <div class="alert alert-error">
        {{ implode('<br />', $errors->all()) }}
    </div>
@endif