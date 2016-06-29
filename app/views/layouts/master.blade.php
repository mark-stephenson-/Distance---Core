<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>The Core</title>

        <!--[if lt IE 9]>
            <script src="/js/html5shiv.js"></script>
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="/css/app.min.css">
        <link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">

        <script src="/js/jquery-1.9.1.js"></script>
        <script> CKEDITOR_BASEPATH = '{{ URL::to('') }}/js/ckeditor/'; </script>
        <script src="/js/app.min.js"></script>
    </head>
    <body>
        <div class="gradient"></div>
        <div class="container-fluid">
        <div class="row-fluid">
            <div class="span2">
                <nav class="main">
                    <a class="logo" href="/"><i class="icon-bullseye"></i></a>
                    <ul>
                        @foreach(Config::get('core-navigation') as $item)
                            <?php
                                $item['access'] = str_replace($item['params'], replaceNavigationParams($item['params']), $item['access']);
                            ?>
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
                            <!-- <li><a href="#"><i class="icon-cog"></i></a></li> -->
                            <li><a href="{{ route('logout') }}"><i class="icon-signout "></i></a></li>
                        </ul>
                    </nav>
                </header>
                <div class="content">
                    <header>
                        @if (count(Collection::all()) and Application::current() and Collection::current())
                            <div class="btn-group change-collection">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    {{ @Collection::current()->name }}
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    @foreach(Application::current()->collectionsWithPermission() as $collection)
                                        <li><a href="{{ switchCollectionUrl($collection->application_id, $collection->id) }}">{{ $collection->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (count(Application::all()) and Application::current())
                            <div class="btn-group change-app">
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    {{ @Application::current()->name }}
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    @foreach(Application::allWithPermission() as $app)
                                        <li><a href="{{ switchAppUrl($app->id) }}">{{ $app->name }}</a></li>
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

    @yield('js')
    
    </body>
</html>