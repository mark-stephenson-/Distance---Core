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

            'Questionnaires' => array(
                'value' => 'cms.questionnaires.*',
                'children' => array(
                    'Can Manage' => array(
                        'value' => 'cms.questionnaires.manage.any',
                    ),
                ),
            ),

            'Domain Nodes' => array(
                'value' => 'cms.apps.1.collections.1.question-domain.*',
                'children' => array(
                    'Can Manage' => array(
                        'value' => 'cms.apps.1.collections.1.question-domain.*',
                    ),
                ),
            ),

            'Volunteers' => array(
                'value' => 'cms.volunteers.*',
                'children' => array(
                    'Can Manage Any' => array(
                        'value' => 'cms.volunteers.manage.any',
                    ),
                    'Can Manage Own' => array(
                        'value' => 'cms.volunteers.manage.own',
                    ),
                ),
            ),

            'Manage Trusts, Hospitals and Wards' => array(
                'value' => 'cms.manage-trust.*',
                'children' => array(
                    'Can Manage Any' => array(
                        'value' => 'cms.manage-trust.manage.any',
                    ),
                    'Can Manage Own' => array(
                        'value' => 'cms.manage-trust.manage.own',
                    ),
                ),
            ),

            'Export Data' => array(
                'value' => 'cms.export-data.*',
                'children' => array(
                    'Can Export Any' => array(
                        'value' => 'cms.export-data.manage.any',
                    ),
                    'Can Export Own' => array(
                        'value' => 'cms.export-data.manage.own',
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
                        'value' => 'cms.users.addgroup',
                    ),
                    'Remove From Group' => array(
                        'value' => 'cms.users.removegroup',
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
