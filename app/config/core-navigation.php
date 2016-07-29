<?php

return array(

    array(
        'title' => 'Apps',
        'route' => 'apps.index',
        'params' => array(),
        'icon' => 'lock',
        'access' => 'cms.apps.*',
    ),
    array(
        'title' => 'Questionnaire management',
        'route' => 'nodes.hierarchy',
        'params' => array('[app-id]', '[collection-id]'),
        'icon' => 'sitemap',
        'access' => 'cms.apps.[app-id].collections.[collection-id].*',
    ),
    array(
        'title' => 'Node List',
        'route' => 'nodes.list',
        'params' => array('[app-id]', '[collection-id]'),
        'icon' => 'th-list',
        'access' => 'cms.apps.[app-id].collections.[collection-id].*',
    ),
    array(
        'title' => 'Collections',
        'route' => 'collections.index',
        'params' => array('[app-id]'),
        'icon' => 'th-large',
        'access' => 'cms.apps.[app-id].collection-management',
    ),
    array(
        'title' => 'Groups',
        'route' => 'groups.index',
        'params' => array(),
        'icon' => 'group',
        'access' => 'cms.groups.*',
    ),
    array(
        'title' => 'Users',
        'route' => 'users.index',
        'params' => array(),
        'icon' => 'user',
        'access' => 'cms.users.*',
    ),
    array(
        'title' => 'Node Types',
        'route' => 'node-types.index',
        'params' => array(),
        'icon' => 'briefcase',
        'access' => 'cms.node-types.*',
    ),
    array(
        'title' => 'App Distribution',
        'route' => 'app-distribution.index',
        'params' => array('[app-id]'),
        'icon' => 'apple',
        'access' => 'cms.apps.[app-id].ota.*',
    ),
    array(
        'title' => 'Catalogues',
        'route' => 'catalogues.index',
        'params' => array('[app-id]', '[collection-id]'),
        'icon' => 'folder-open',
        'access' => 'cms.apps.[app-id].collections.[collection-id].catalogues.*',
    ),
    array(
        'title' => 'Resources',
        'route' => 'resources.index',
        'params' => array('[app-id]', '[collection-id]'),
        'icon' => 'file',
        'access' => 'cms.apps.[app-id].collections.[collection-id].catalogues.*',
    ),
    array(
        'title' => 'Manage Volunteers',
        'route' => 'volunteers.index',
        'params' => array(),
        'icon' => 'group',
        'access' => 'cms.volunteers.*',
    ),
    array(
        'title' => 'Trusts, Hospitals & Wards',
        'route' => 'manage.index',
        'params' => array(),
        'icon' => 'hospital',
        'access' => 'cms.manage-trust.*',
    ),
    array(
        'title' => 'Reporting',
        'route' => 'reporting.index',
        'params' => [],
        'icon' => 'bar-chart',
        'access' => 'cms.export-data.*',
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
