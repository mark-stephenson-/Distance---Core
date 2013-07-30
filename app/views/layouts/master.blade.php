<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>The Core</title>

        <!--[if lt IE 9]>
            <script src="/js/html5shiv.js"></script>
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="/css/app.min.css">
    </head>
    <body>

        <div class="container-fluid">
        <div class="row-fluid">
            <div class="span2">
                <nav class="main">
                    <a class="logo" href="/"><img src="/images/logo.png" /></a>
                    <ul>
                        @foreach(Config::get('core-navigation') as $item)
                            @if (Sentry::getUser()->hasAccess($item['access']))
                                <li><a href="{{ route($item['route'], replaceNavigationParams($item['params'])) }}"><i class="icon-{{ $item['icon'] }}"></i> {{ $item['title']}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
            <div class="span10">
                <header class="meta">
                    <nav>
                        <ul>
                            <li><a href="{{ route('me') }}">{{ Sentry::getUser()->full_name ?: Sentry::getUser()->email }}</a></li>
                            <li><a href="#"><i class="icon-cog"></i></a></li>
                            <li><a href="{{ route('logout') }}"><i class="icon-signout "></i></a></li>
                        </ul>
                    </nav>
                </header>
                <div class="content">
                    <header>
                        @if ( count(Collection::all()) )
                            <div class="btn-group change-collection">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    {{ Collection::current()->name }}
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    @foreach(Collection::all() as $collection)
                                        <li><a href="{{ route('nodes.' . Config::get('core.prefrences.preferred-node-view'), [$collection->id]) }}">{{ $collection->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('header')
                    </header>
                    <section class="body">
                        <div class="row-fluid">
                            <div class="span12">
                                @include('partials.alerts')

                                @yield('body')
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade hide" id="deleteModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Collection Name Goes Here</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this collection? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn btn-primary">Yes, Delete it.</a>
        </div>
    </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script> CKEDITOR_BASEPATH = '{{ URL::to('') }}/js/ckeditor/'; </script>
        <script src="/js/app.min.js"></script>
        @yield('js')

    </body>
</html>