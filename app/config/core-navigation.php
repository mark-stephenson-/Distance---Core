<?php

return [
    
    [
        'title'     => 'Hierarchy',
        'route'     => 'nodes.hierarchy',
        'params'    => ['[collection-id]'],
        'icon'      => 'sitemap',
        'access'    => null,
    ],
    [
        'title'     => 'Node List',
        'route'     => 'nodes.list',
        'params'    => ['[collection-id]'],
        'icon'      => 'th-list',
        'access'    => null,
    ],
    [
        'title'     => 'Collections',
        'route'     => 'collections.index',
        'params'    => [],
        'icon'      => 'th-large',
        'access'    => null,
    ],
    [
        'title'     => 'Groups',
        'route'     => 'groups.index',
        'params'    => [],
        'icon'      => 'group',
        'access'    => 'cms.groups.*',
    ],
    [
        'title'     => 'Users',
        'route'     => 'users.index',
        'params'    => [],
        'icon'      => 'user',
        'access'    => 'cms.users.*',
    ],
    [
        'title'     => 'Node Types',
        'route'     => 'node-types.index',
        'params'    => [],
        'icon'      => 'briefcase',
        'access'    => 'superuser',
    ],
    [
        'title'     => 'Apps',
        'route'     => 'apps.index',
        'params'    => [],
        'icon'      => 'lock',
        'access'    => 'cms.apps.*',
    ],
    [
        'title'     => 'App Distribution',
        'route'     => 'app-distribution.index',
        'params'    => [],
        'icon'      => 'apple',
        'access'    => null,
    ],
    [
        'title'     => 'Catalogues',
        'route'     => 'catalogues.index',
        'params'    => [],
        'icon'      => 'folder-open',
        'access'    => null,
    ],
    [
        'title'     => 'Resources',
        'route'     => 'resources.index',
        'params'    => [],
        'icon'      => 'file',
        'access'    => null,
    ],
    [
        'title'     => 'Templates',
        'route'     => 'nodes.type-list',
        'params'    => ['[collection-id]', 'template'],
        'icon'      => 'code',
        'access'    => null,
    ],

    /*
        Custom Node Type Navigation Item

        You only need to change the title, second param, icon and optionally access
     */
    // [
    //     'title'     => 'Pages',
    //     'route'     => 'nodes.type-list',
    //     'params'    => ['[collection-id]', 'content-page'],
    //     'icon'      => 'file',
    //     'access'    => null,
    // ],


];