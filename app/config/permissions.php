<?php

return array(
    
    'CMS Access' => array(
        'value' => 'cms.*',
        'children' => array(

            'Users' => array(
                'value' => 'cms.users.*',
                'children' => array(
                    
                    'Create' => array(
                        'value' => 'cms.users.create'
                    ),
                    'Read' => array(
                        'value' => 'cms.users.read'
                    ),
                    'Update' => array(
                        'value' => 'cms.users.update'
                    ),
                    'Delete' => array(
                        'value' => 'cms.users.delete'
                    ),

                ),
            ),

            'Groups' => array(
                'value' => 'cms.groups.*',
                'children' => array(
                    
                    'Create' => array(
                        'value' => 'cms.groups.create'
                    ),
                    'Read' => array(
                        'value' => 'cms.groups.read'
                    ),
                    'Update' => array(
                        'value' => 'cms.groups.update'
                    ),
                    'Delete' => array(
                        'value' => 'cms.groups.delete'
                    ),

                ),
            ),

            'Collections' => array(
                'value' => 'cms.collections.*',
                'children' => array(
                    
                    'Create' => array(
                        'value' => 'cms.collections.create'
                    ),
                    'Read' => array(
                        'value' => 'cms.collections.read'
                    ),
                    'Update' => array(
                        'value' => 'cms.collections.update'
                    ),
                    'Delete' => array(
                        'value' => 'cms.collections.delete'
                    ),

                ),
            ),

            'Catalogues' => array(
                'value' => 'cms.catalogues.*',
                'children' => array(
                    
                    'Create' => array(
                        'value' => 'cms.catalogues.create'
                    ),
                    'Read' => array(
                        'value' => 'cms.catalogues.read'
                    ),
                    'Update' => array(
                        'value' => 'cms.catalogues.update'
                    ),
                    'Delete' => array(
                        'value' => 'cms.catalogues.delete'
                    ),

                ),
            ),

            'Resources' => array(
                'value' => 'cms.resources.*',
                'children' => array(
                    
                    'Create' => array(
                        'value' => 'cms.resources.create'
                    ),
                    'Read' => array(
                        'value' => 'cms.resources.read'
                    ),
                    'Update' => array(
                        'value' => 'cms.resources.update'
                    ),
                    'Delete' => array(
                        'value' => 'cms.resources.delete'
                    ),

                ),
            ),

        ),
    ),

    'API Access' => array(
        'value' => 'api.*',
    ),

);