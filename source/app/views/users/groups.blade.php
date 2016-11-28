<?php
$currentGroups = $user->getGroups()->lists('id', 'name');
?>

<div class="row">
    <ul class="span8 offset2 groups">
        @foreach ( $groups as $group )
            <li>
                <div class="heading">
                    <p class="lead pull-left">{{ $group->name }}</p>
                    <div class="pull-right">
                        @if ( in_array($group->id, $currentGroups) )
                            @if ( Sentry::getUser()->hasAccess('cms.users.removegroup') )
                                <a href="{{ route('users.remove-group', array($user->id, $group->id)) }}" class="btn btn-danger">Remove from Group</a>
                            @endif
                        @else
                            @if ( Sentry::getUser()->hasAccess('cms.users.addgroup') )
                                <a href="{{ route('users.add-group', array($user->id, $group->id)) }}" class="btn btn-success">Add to Group</a>
                            @endif
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>