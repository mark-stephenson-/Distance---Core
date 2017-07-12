@extends('layouts.master')

@section('header')
    <h1>PRASE</h1>
@stop

@section('body')

  <img src="/images/logos/prase-logo.png" alt="Prase logo" width="200" style="padding:20px;">
  <img src="/images/logos/improvement.jpg" alt="Improvement" width="200" style="padding:20px;">
  <img src="/images/logos/yqsr.jpg" alt="Health Foundation logo" width="200" style="padding:20px;">
  <img src="/images/logos/health_foundation.jpg" alt="Health Foundation logo" width="200" style="padding:20px;">


  <div class="title-block">
      <h3>Welcome to the PRASE Report Management System</h3>
    </div>

    <?php
    $hpo = Sentry::findGroupByName('Health Professional Observer');
    $hua = Sentry::findGroupByName('Healthcare Unit Admin');
    $su = Sentry::findGroupByName('Super User');
    ?>

    @if (Sentry::getUser()->isSuperUser())
    <p class="pull-right">
        <a href="{{ route('apps.create') }}" class="btn"><i class="icon-plus"></i> New App</a>
    </p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>API Key</th>
                <th>Collections</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apps as $app)
                <?php
                    $collectionAccess = array();
                    $collectionAccess[] = 'cms.apps.' . $app->id . '.collection-management';

                    foreach($app->collections as $collection) {
                        $collectionAccess[] = 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.*';
                    }
                ?>
                @if (Sentry::getUser()->hasAnyAccess($collectionAccess))
                    <tr>
                        <td>
                            {{ $app->name }}
                        </td>
                        <td>
                            {{ $app->api_key }}
                        </td>
                        <td>
                            @foreach($app->collections as $collection)
                                @if (Sentry::getUser()->hasAnyAccess(array('cms.apps.' . $app->id . '.collections.' . $collection->id . '.*', 'cms.apps.' . $app->id . '.collection-management')))
                                    {{ $collection->name }}<br />
                                @endif
                            @endforeach
                        </td>
                        <td width="250">
                            <a href="{{ route('collections.index', array($app->id)) }}" class="btn btn-small"><i class="icon-th-large"></i> Collections</a>
                            <a href="{{ route('apps.edit', array($app->id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                            <a href="#deleteModal" class="btn btn-small deleteModal" data-name="{{ $app->name }}" data-id="{{ $app->id }}"><i class="icon-trash"></i> Delete</a>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @elseif(Sentry::getUser()->inGroup($su))
        @include('partials.login_SU')
    @elseif(Sentry::getUser()->inGroup($hua))
        @include('partials.login_HUA')
    @elseif(Sentry::getUser()->inGroup($hpo))
        @include('partials.login_HPO')
    @endif

    <div class="modal fade hide" id="deleteModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3></h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this app? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="" class="btn btn-primary yes">Yes, Delete it.</a>
        </div>
    </div>

    <script>
    $(document).ready( function() {
            $(".deleteModal").click( function(e) {
                var data_id = $(this).attr('data-id');
                var data_name = $(this).attr('data-name');
                var url = '{{ route('app.destroy', 'id') }}';

                $("#deleteModal").find('h3').html( "Delete app <small>" + data_name + "</small>");
                $("#deleteModal").find('.yes').attr('href', url.replace('id', data_id));

                $("#deleteModal").modal('show');
            });
        });
    </script>

@stop
