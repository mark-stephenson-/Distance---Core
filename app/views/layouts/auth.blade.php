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
                <div class="content auth span5 offset3">
                        <header>
                            @yield('header')
                        </header>
                        <section class="body">
                            @include('partials.alerts')
                            @yield('body')
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="/js/app.min.js"></script>

    </body>
</html>