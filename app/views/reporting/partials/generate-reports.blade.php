<div class="clearfix">

    <div class="title-block">
        <h3>Bespoke Report Parameters</h3>
        <p>You must select a date range and one ward to generate a report.</p>
    </div>

    <div class="reports-form">
        {{ Form::open(['id' => 'report-form']) }}
        <div class="span9 filter-container">
            <div class="row">
                {{ Form::label('period_start', 'Report Period', array('class' => 'control-label span2')) }}
                <div class="controls">
                    <div class="input-group span4">
                        <i class="icon-calendar" data-trigger="period_start"></i>
                        {{ Form::text('period_start', Input::old('period_start'), array('class' => 'span12', 'id' => 'period_start')) }}
                    </div>
                    <div class="input-group span4">
                        <i class="icon-calendar" data-trigger="period_end"></i>
                        {{ Form::text('period_end', Input::old('period_end'), array('class' => 'span12', 'id' => 'period_end')) }}
                    </div>
                </div>
            </div>
            <div class="row">
                {{ Form::label('trust', 'Location', array('class' => 'control-label span2')) }}
                <div class="controls">
                    <div class="input-group span4">
                        {{ Form::select('trust', $trusts, Input::old('trust'), array('class' => 'trust-select span12')) }}
                    </div>
                    <div class="input-group span4">
                        {{ Form::select('hospital', array(), Input::old('hospital'), array('class' => 'hospital-select span12', 'style' => 'display: none;')) }}
                    </div>
                </div>
            </div>
            <div class="row wards-row hide">
                {{ Form::label('wards', 'Ward', array('class' => 'control-label span2')) }}
                <div class="controls">
                    <div class="input-group span4">
                        {{ Form::hidden('wards', null, ['id' => 'wards', 'class' => 'wards-select span12']) }}
                    </div>
                </div>
            </div>

        </div>
        <div class="span2">
            {{ Form::submit('Generate Report', array('class' => 'submit-button btn', 'id' => 'generate', 'disabled')) }}
        </div>
        {{ Form::close() }}
    </div>
</div>