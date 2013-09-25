<?php

return array(
    
    array(
        'title'     => 'Apps',
        'route'     => 'apps.index',
        'params'    => array(),
        'icon'      => 'lock',
        'access'    => 'cms.apps.*',
    ),
    array(
        'title'     => 'Hierarchy',
        'route'     => 'nodes.hierarchy',
        'params'    => array('[app-id]', '[collection-id]'),
        'icon'      => 'sitemap',
        'access'    => 'cms.apps.[app-id].collections.[collection-id].*',
    ),
    array(
        'title'     => 'Node List',
        'route'     => 'nodes.list',
        'params'    => array('[app-id]', '[collection-id]'),
        'icon'      => 'th-list',
        'access'    => 'cms.apps.[app-id].collections.[collection-id].*',
    ),
    array(
        'title'     => 'Collections',
        'route'     => 'collections.index',
        'params'    => array('[app-id]'),
        'icon'      => 'th-large',
        'access'    => 'cms.apps.[app-id].collection-management',
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
        'access'    => 'cms.node-types.*',
    ),
    array(
        'title'     => 'App Distribution',
        'route'     => 'app-distribution.index',
        'params'    => array('[app-id]'),
        'icon'      => 'apple',
        'access'    => 'cms.apps.[app-id].ota.*',
    ),
    array(
        'title'     => 'Catalogues',
        'route'     => 'catalogues.index',
        'params'    => array('[app-id]', '[collection-id]'),
        'icon'      => 'folder-open',
        'access'    => 'cms.apps.[app-id].collections.[collection-id].catalogues.*',
    ),
    array(
        'title'     => 'Resources',
        'route'     => 'resources.index',
        'params'    => array('[app-id]', '[collection-id]', '[catalogue-id]'),
        'icon'      => 'file',
        'access'    => 'cms.apps.[app-id].collections.[collection-id].catalogues.[catalogue-id].*',
    ),
    array(
        'title'     => 'Templates',
        'route'     => 'nodes.type-list',
        'params'    => array('[app-id]', '[collection-id]', 'template'),
        'icon'      => 'code',
        'access'    => 'cms.apps.[app-id].collections.[collection-id].*',
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