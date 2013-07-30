<?php

return [
    
    [
        'title'     => 'Collections',
        'route'     => 'collections.index',
        'params'    => [],
        'icon'      => 'th-large',
        'access'    => '',
    ],
    [
        'title'     => 'Groups',
        'route'     => 'groups.index',
        'params'    => [],
        'icon'      => 'group',
        'access'    => 'superuser',
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
        'route'     => 'collections.index',
        'params'    => [],
        'icon'      => 'apple',
        'access'    => '',
    ],
    [
        'title'     => 'Catalogues',
        'route'     => 'catalogues.index',
        'params'    => [],
        'icon'      => 'folder-open',
        'access'    => '',
    ],
    [
        'title'     => 'Resources',
        'route'     => 'resources.index',
        'params'    => [],
        'icon'      => 'file',
        'access'    => '',
    ],


];