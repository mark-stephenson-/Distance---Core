<?php

return array(
    
    array(
        'title'     => 'Hierarchy',
        'route'     => 'nodes.hierarchy',
        'params'    => array('[collection-id]'),
        'icon'      => 'sitemap',
        'access'    => null,
    ),
    array(
        'title'     => 'Node List',
        'route'     => 'nodes.list',
        'params'    => array('[collection-id]'),
        'icon'      => 'th-list',
        'access'    => null,
    ),
    array(
        'title'     => 'Collections',
        'route'     => 'collections.index',
        'params'    => array(),
        'icon'      => 'th-large',
        'access'    => null,
    ),
    array(
        'title'     => 'Groups',
        'route'     => 'groups.index',
        'params'    => array(),
        'icon'      => 'group',
        'access'    => 'cms.groups.*',
    ),
    array(
        'title'     => 'Users',
        'route'     => 'users.index',
        'params'    => array(),
        'icon'      => 'user',
        'access'    => 'cms.users.*',
    ),
    array(
        'title'     => 'Node Types',
        'route'     => 'node-types.index',
        'params'    => array(),
        'icon'      => 'briefcase',
        'access'    => 'superuser',
    ),
    array(
        'title'     => 'Apps',
        'route'     => 'apps.index',
        'params'    => array(),
        'icon'      => 'lock',
        'access'    => 'cms.apps.*',
    ),
    array(
        'title'     => 'App Distribution',
        'route'     => 'app-distribution.index',
        'params'    => array(),
        'icon'      => 'apple',
        'access'    => null,
    ),
    array(
        'title'     => 'Catalogues',
        'route'     => 'catalogues.index',
        'params'    => array(),
        'icon'      => 'folder-open',
        'access'    => 'cms.catalogues.*',
    ),
    array(
        'title'     => 'Resources',
        'route'     => 'resources.index',
        'params'    => array(),
        'icon'      => 'file',
        'access'    => 'cms.resources.*',
    ),
    array(
        'title'     => 'Templates',
        'route'     => 'nodes.type-list',
        'params'    => array('[collection-id]', 'template'),
        'icon'      => 'code',
        'access'    => null,
    ),

    /*
        Custom Node Type Navigation Item

        You only need to change the title, second param, icon and optionally access
     */
    // array(
    //     'title'     => 'Pages',
    //     'route'     => 'nodes.type-list',
    //     'params'    => array('[collection-id]', 'content-page'),
    //     'icon'      => 'file',
    //     'access'    => null,
    // ),


);