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
                    <a class="logo" href="/"><i class="icon-css3"></i></a>
                    <ul>
                        <li><a href="#"><i class="icon-th-large"></i> Collections</a></li>
                        <li><a href="#"><i class="icon-group"></i> Users</a></li>
                        <li><a href="#"><i class="icon-briefcase"></i> Node Types</a></li>
                        <li><a href="#"><i class="icon-lock"></i> API Keys</a></li>
                        <li><a href="#"><i class="icon-apple"></i> App Distribution</a></li>
                        <li><a href="#"><i class="icon-folder-open"></i> Resources</a></li>
                    </ul>
                </nav>
            </div>
            <div class="span10">
                <header class="meta">
                    <nav>
                        <ul>
                            <li><a href="#">Sam Jordan</a></li>
                            <li><a href="#"><i class="icon-cog"></i></a></li>
                            <li><a href="#"><i class="icon-signout "></i></a></li>
                        </ul>
                    </nav>
                </header>
                <div class="content">
                    <header>
                        <div class="btn-group change-collection">
                            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                Leeds Teaching Hospital NHS Trust
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#">Bradford Teaching Hospitals NHS Foundation Trust</a></li>
                                <li><a href="#">Demo - St Elsewhere NHS Trust</a></li>
                            </ul>
                        </div>

                        <h1> Collections</h1>
                    </header>
                    <section class="body">
                        <div class="row-fluid">
                            <div class="span12">
                                <p>Etiam porta sem malesuada magna mollis euismod. Maecenas sed diam eget risus varius blandit sit amet non magna. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Donec id elit non mi porta gravida at eget metus.</p>

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                Leeds Teaching Hospital NHS Trust
                                            </td>
                                            <td width="330">
                                                <a href="#" class="btn btn-small"><i class="icon-sitemap"></i> Hierarchy</a>
                                                <a href="#" class="btn btn-small"><i class="icon-list"></i> Node List</a>
                                                <a href="#" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                                                <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Bradford Teaching Hospitals NHS Foundation Trust
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-small"><i class="icon-sitemap"></i> Hierarchy</a>
                                                <a href="#" class="btn btn-small"><i class="icon-list"></i> Node List</a>
                                                <a href="#" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                                                <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Demo - St Elsewhere NHS Trust
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-small"><i class="icon-sitemap"></i> Hierarchy</a>
                                                <a href="#" class="btn btn-small"><i class="icon-list"></i> Node List</a>
                                                <a href="#" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
                                                <a href="#deleteModal" data-toggle="modal" class="btn btn-small"><i class="icon-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
        <script src="/js/app.min.js"></script>

    </body>
</html>