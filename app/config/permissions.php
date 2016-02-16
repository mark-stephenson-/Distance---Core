<?php

return array(

    'CMS Global Permissions' => array(
        'value' => 'cms.*',
        'children' => array(

            'Generic' => array(
                'value' => 'cms.generic.*',
                'children' => array(
                    'Can Login' => array(
                        'value' => 'cms.generic.login',
                    ),
                ),
            ),

            'Node Types' => array(
                'value' => 'cms.node-types.*',
                'children' => array(

                    'Create' => array(
                        'value' => 'cms.node-types.create',
                    ),
                    'Read' => array(
                        'value' => 'cms.node-types.read',
                    ),
                    'Update' => array(
                        'value' => 'cms.node-types.update',
                    ),
                    'Delete' => array(
                        'value' => 'cms.node-types.delete',
                    ),

                ),
            ),

            'Volunteers' => array(
                'value' => 'cms.volunteers.*',
                'children' => array(
                    'Manage' => array(
                        'value' => 'cms.volunteers.manage',
                    ),
                ),
            ),

            'Manage Trusts, Hospitals and Wards' => array(
                'value' => 'cms.manage-trust.*',
                'children' => array(
                    'Manage' => array(
                        'value' => 'cms.manage-trust.manage',
                    ),
                ),
            ),

            'Export Data' => array(
                'value' => 'cms.export-data.*',
                'children' => array(
                    'Export' => array(
                        'value' => 'cms.export-data.export',
                    ),
                ),
            ),

            'Users' => array(
                'value' => 'cms.users.*',
                'children' => array(

                    'Create' => array(
                        'value' => 'cms.users.create',
                    ),
                    'Read' => array(
                        'value' => 'cms.users.read',
                    ),
                    'Update' => array(
                        'value' => 'cms.users.update',
                    ),
                    'Delete' => array(
                        'value' => 'cms.users.delete',
                    ),
                    'Add To Group' => array(
                        'value' => 'cms.users.removegroup',
                    ),
                    'Remove From Group' => array(
                        'value' => 'cms.users.addgroup',
                    ),

                ),
            ),

            'Groups' => array(
                'value' => 'cms.groups.*',
                'children' => array(

                    'Create' => array(
                        'value' => 'cms.groups.create',
                    ),
                    'Read' => array(
                        'value' => 'cms.groups.read',
                    ),
                    'Update' => array(
                        'value' => 'cms.groups.update',
                    ),
                    'Delete' => array(
                        'value' => 'cms.groups.delete',
                    ),

                ),
            ),

        ),
    ),

    // 'API Access' => array(
    //     'value' => 'api.*',
    // ),

);
