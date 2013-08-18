<?php

return [
    
    'CMS Access' => [
        'value' => 'cms.*',
        'children' => [

            'Users' => [
                'value' => 'cms.users.*',
                'children' => [
                    
                    'Create' => [
                        'value' => 'cms.users.create'
                    ],
                    'Read' => [
                        'value' => 'cms.users.read'
                    ],
                    'Update' => [
                        'value' => 'cms.users.update'
                    ],
                    'Delete' => [
                        'value' => 'cms.users.delete'
                    ],

                ],
            ],

            'Groups' => [
                'value' => 'cms.groups.*',
                'children' => [
                    
                    'Create' => [
                        'value' => 'cms.groups.create'
                    ],
                    'Read' => [
                        'value' => 'cms.groups.read'
                    ],
                    'Update' => [
                        'value' => 'cms.groups.update'
                    ],
                    'Delete' => [
                        'value' => 'cms.groups.delete'
                    ],

                ],
            ],

            'Collections' => [
                'value' => 'cms.collections.*',
                'children' => [
                    
                    'Create' => [
                        'value' => 'cms.collections.create'
                    ],
                    'Read' => [
                        'value' => 'cms.collections.read'
                    ],
                    'Update' => [
                        'value' => 'cms.collections.update'
                    ],
                    'Delete' => [
                        'value' => 'cms.collections.delete'
                    ],

                ],
            ],

            'Catalogues' => [
                'value' => 'cms.catalogues.*',
                'children' => [
                    
                    'Create' => [
                        'value' => 'cms.catalogues.create'
                    ],
                    'Read' => [
                        'value' => 'cms.catalogues.read'
                    ],
                    'Update' => [
                        'value' => 'cms.catalogues.update'
                    ],
                    'Delete' => [
                        'value' => 'cms.catalogues.delete'
                    ],

                ],
            ],

        ],
    ],

    'API Access' => [
        'value' => 'api.*',
    ],

];