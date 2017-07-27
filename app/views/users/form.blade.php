@extends('layouts.master')

@section('header')
    @if ($user->exists)
        <h1>Editing User</h1>
    @else
        <h1>New User</h1>
    @endif
@stop

@section('body')
    
    {{ formModel($user, 'users', array('autocomplete' => 'off'), false) }}

    <ul class="nav nav-tabs">
        <li class="active"><a href="#info" data-toggle="tab">User Info</a></li>

        @if ( $user->exists )
            <li><a href="#groups" data-toggle="tab">Groups</a></li>
            <li><a href="#permissions" data-toggle="tab">Trust, Hospitals and Wards Permissions</a></li>
        @endif

    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="info">

            @if (Sentry::getUser()->isSuperUser())
                <div class="control-group">
                    {{ Form::label('super_admin', 'Super Admin', array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::checkbox('super_admin', true, $user->isSuperUser()) }}
                    </div>
                </div>
            @endif

            <div class="control-group">
                {{ Form::label('first_name', 'First Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('first_name', null, array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('last_name', 'Last Name', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('last_name', null, array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('email', 'Email Address', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::text('email', null, array('class' => 'span11')) }}
                </div>
            </div>

            @if ($field_1 = Config::get('core.labels.user_field_1'))
                <div class="control-group">
                    {{ Form::label('field_1', $field_1, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_1', Input::old('field_1', $user->field_1), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_2 = Config::get('core.labels.user_field_2'))
                <div class="control-group">
                    {{ Form::label('field_2', $field_2, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_2', Input::old('field_2', $user->field_2), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_3 = Config::get('core.labels.user_field_3'))
                <div class="control-group">
                    {{ Form::label('field_3', $field_3, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_3', Input::old('field_3', $user->field_3), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_4 = Config::get('core.labels.user_field_4'))
                <div class="control-group">
                    {{ Form::label('field_4', $field_4, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_4', Input::old('field_4', $user->field_4), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            @if ($field_5 = Config::get('core.labels.user_field_5'))
                <div class="control-group">
                    {{ Form::label('field_5', $field_5, array('class' => 'control-label')) }}
                    <div class="controls">
                        {{ Form::text('field_5', Input::old('field_5', $user->field_5), array('class' => 'span11')) }}
                    </div>
                </div>
            @endif

            <hr />

            @if ($user->exists)
                <div class="alert">
                    Only enter a password if you want to change it.
                </div>
            @endif

            <div class="control-group">
                {{ Form::label('password', 'Password', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::password('password', null, array('class' => 'span11')) }}
                </div>
            </div>

            <div class="control-group">
                {{ Form::label('password_confirmation', 'Password Confirmation', array('class' => 'control-label')) }}
                <div class="controls">
                    {{ Form::password('password_confirmation', null, array('class' => 'span11')) }}
                </div>
            </div> 

        </div>

        @if ( $user->exists )
            <div class="tab-pane" id="groups">
                @include('users.groups')
            </div>

            <div class="tab-pane" id="permissions">
                @unless($trusts->isEmpty())
                    <ul class="unstyled">
                        @foreach($trusts as $trust)
                            <li>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="nodes-checkbox" data-node-checkbox="{{ $trust->node_id }}" name="node_ids[]" value="{{ $trust->node_id }}" {{ in_array($trust->node_id, $user->accessible_nodes) ? 'checked' : '' }}>{{ $trust->name }}
                                        <a data-toggle="collapse" class="arrow-link" href="#collapse_hospitals_{{ $trust->node_id }}" aria-expanded="false" aria-controls="collapse_hospitals_{{ $trust->node_id }}"><i class="icon-caret-right"></i></a>
                                    </label>
                                </div>
                                @unless($trust->hospitals->isEmpty())
                                    <ul class="collapse" id="collapse_hospitals_{{$trust->node_id}}">
                                        @foreach($trust->hospitals as $hospital)
                                            <li>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="nodes-checkbox" data-node-checkbox="{{ $hospital->node_id }}" name="node_ids[]" data-parent-node="{{ $trust->node_id }}" value="{{ $hospital->node_id }}" {{ in_array($hospital->node_id, $user->accessible_nodes) ? 'checked' : '' }}>{{ $hospital->name }}
                                                        @unless($hospital->wards->isEmpty())
                                                            <a data-toggle="collapse" class="arrow-link" href="#collapse_wards_{{ $hospital->node_id }}" aria-expanded="false" aria-controls="collapse_wards_{{ $hospital->node_id }}"><i class="icon-caret-right"></i></a>
                                                        @endif
                                                    </label>
                                                </div>
                                                @unless($hospital->wards->isEmpty())
                                                    <ul class="collapse" id="collapse_wards_{{$hospital->node_id}}">
                                                        @foreach($hospital->wards as $ward)
                                                            <li>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="nodes-checkbox" data-node-checkbox="{{ $ward->node_id }}" name="node_ids[]" data-parent-node="{{ $hospital->node_id }}" value="{{ $ward->node_id }}" {{ in_array($ward->node_id, $user->accessible_nodes) ? 'checked' : '' }}>{{ $ward->name }}
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endunless
                                            </li>
                                        @endforeach
                                    </ul>
                                @endunless
                            </li>
                        @endforeach
                    </ul>
                @endunless
            </div>
        @endif

    </div>   

    <div class="form-actions">
        @if ($user->exists)
            <input type="submit" class="btn btn-primary" value="Save changes" />
        @else
            <input type="submit" class="btn btn-primary" value="Create User" />
        @endif
    </div>

    {{ Form::close() }}

    <script>
        $('document').ready(function() {
            // init the collapse elements
            $('.collapse').collapse({toggle: false});

            // make sure that if a child element is checked, the parent gets checked as well
            $('.nodes-checkbox').on('change', function () {
                var $sibblings_list = $(this).closest('ul').children('li');
                var $parent_checkbox = $('#permissions').find('[data-node-checkbox="' + $(this).data('parent-node') + '"]');

                // filter through the parent children elem and get the checked ones
                var checked_sibblings = $sibblings_list.filter(function(key, item) {
                    return $(item).find('.nodes-checkbox').prop('checked');
                });

                // check/uncheck parent checkboxes all the way up to root
                if(checked_sibblings.length) {
                    $parent_checkbox.prop('checked', true);
                } else {
                    $parent_checkbox.prop('checked', false);
                }
                $parent_checkbox.trigger('change');
            });

            $('.arrow-link').click(function () {
                var $self = $(this)
                setTimeout(function() {
                    if($self.hasClass('collapsed')) {
                        $self.children('i').removeClass('icon-caret-down').addClass('icon-caret-right');
                    } else {
                        $self.children('i').removeClass('icon-caret-right').addClass('icon-caret-down');
                    }

                }, 50)
            })
        });
    </script>
@stop
